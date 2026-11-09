<?php

class DocumentController extends Controller
{
    private const MAX_SIZE = 2097152;

    private ?PDO $pdo = null;

    private function db(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = (new Database)->getConnection();
        }

        return $this->pdo;
    }

    private function userId(): string
    {
        return $_SESSION['user']['id'];
    }

    private function backWithError(string $msg): void
    {
        $_SESSION['error'] = $msg;
        header('Location: /Tandain/document/create');
        exit;
    }

    public function index()
    {
        $this->authCheck();

        $documentModel = $this->model('Document');
        $userId = $_SESSION['user']['id'];

        $search = trim($_GET['q'] ?? '');
        $filter = $_GET['status'] ?? 'all';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 8;
        $offset = ($page - 1) * $perPage;

        $total = $documentModel->countUserDocuments($userId, $search, $filter);
        $totalPages = max(1, (int) ceil($total / $perPage));

        $data['title'] = 'Dokumen Diunggah - Tandain';
        $data['active_menu'] = 'uploaded';
        $data['user'] = $_SESSION['user'];
        $data['documents'] = $documentModel->getUserDocuments($userId, $search, $filter, $perPage, $offset);
        $data['counts'] = $documentModel->getUserStatusCounts($userId);
        $data['search'] = $search;
        $data['filter'] = $filter;
        $data['page'] = $page;
        $data['perPage'] = $perPage;
        $data['total'] = $total;
        $data['totalPages'] = $totalPages;

        $this->view('document/index', $data);
    }

    public function create()
    {
        $this->authCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }

        $stmt = $this->db()->query(
            "SELECT u.id, u.full_name, u.identity_number, u.email, u.position
             FROM users u
             JOIN roles r ON r.id = u.role_id
             WHERE r.name = 'pegawai' AND u.status = 'ACTIVE'
             ORDER BY u.full_name"
        );

        $data['title'] = 'Ajukan Dokumen - Tandain';
        $data['active_menu'] = 'apply';
        $data['user'] = $_SESSION['user'];
        $data['candidates'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data['error'] = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('document/create', $data);
    }

    private function store()
    {
        if (($_POST['submission_mode'] ?? 'upload') !== 'upload') {
            $this->backWithError('Mode template surat belum tersedia.');
        }

        $title = trim($_POST['title'] ?? '');
        $expiresAt = trim($_POST['expires_at'] ?? '') ?: null;
        $isTriage = !empty($_POST['is_triage']);
        $sequential = !empty($_POST['is_sequential']);
        $signerIds = array_values(array_unique($_POST['signers'] ?? []));

        if ($title === '') {
            $this->backWithError('Judul dokumen wajib diisi.');
        }

        if (!$isTriage && count($signerIds) === 0) {
            $this->backWithError('Pilih minimal satu penandatangan.');
        }

        $file = $_FILES['document_file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $this->backWithError('Berkas PDF wajib diunggah.');
        }

        if ($file['size'] > self::MAX_SIZE) {
            $this->backWithError('Ukuran berkas maksimal 2 MB.');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

        if ($mime !== 'application/pdf') {
            $this->backWithError('Berkas harus berformat PDF.');
        }

        $dir = dirname(__DIR__) . '/uploads/documents/';

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.pdf';

        if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            $this->backWithError('Gagal menyimpan berkas.');
        }

        $hash = hash_file('sha256', $dir . $filename);
        $pdo = $this->db();

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                'INSERT INTO documents
                 (title, requester_id, original_file_path, initial_sha256_hash,
                  status, routing_type, is_sequential, expires_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?) RETURNING id'
            );

            $stmt->execute([
                $title,
                $this->userId(),
                'uploads/documents/' . $filename,
                $hash,
                $isTriage ? 'PENDING_TRIAGE' : 'DRAFT',
                $isTriage ? 'TRIAGE_ASSISTED' : 'DIRECT',
                $sequential ? 'true' : 'false',
                $expiresAt,
            ]);

            $docId = $stmt->fetchColumn();

            if (!$isTriage) {
                $ins = $pdo->prepare(
                    'INSERT INTO document_signers (document_id, signer_id, signing_order)
                     VALUES (?, ?, ?)'
                );

                foreach ($signerIds as $i => $sid) {
                    $ins->execute([$docId, $sid, $i + 1]);
                }
            }

            $pdo->prepare(
                'INSERT INTO document_audit_logs (document_id, user_id, action, notes)
                 VALUES (?, ?, ?, ?)'
            )->execute([$docId, $this->userId(), 'CREATED', 'Dokumen diajukan']);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            @unlink($dir . $filename);
            $this->backWithError('Gagal menyimpan dokumen: ' . $e->getMessage());
        }

        if ($isTriage) {
            header('Location: /Tandain/document');
        } else {
            header('Location: /Tandain/document/position/' . $docId);
        }

        exit;
    }

    public function position($id = null)
    {
        $this->authCheck();
        $doc = $this->findOwnedDocument($id);

        $stmt = $this->db()->prepare(
            'SELECT ds.signer_id AS id, ds.signing_order, u.full_name AS name
             FROM document_signers ds
             JOIN users u ON u.id = ds.signer_id
             WHERE ds.document_id = ?
             ORDER BY ds.signing_order'
        );

        $stmt->execute([$doc['id']]);

        $data['title'] = 'Atur Posisi TTE - Tandain';
        $data['active_menu'] = 'apply';
        $data['user'] = $_SESSION['user'];
        $data['documentId'] = $doc['id'];
        $data['signers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $data['pdfUrl'] = '/Tandain/document/file/' . $doc['id'];
        $data['error'] = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('document/position', $data);
    }

    public function file($id = null)
    {
        $this->authCheck();
        $doc = $this->findOwnedDocument($id);

        $path = dirname(__DIR__) . '/' . $doc['original_file_path'];

        if (!is_file($path)) {
            http_response_code(404);
            exit('Berkas tidak ditemukan.');
        }

        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public function savePositions($id = null)
    {
        $this->authCheck();
        $doc = $this->findOwnedDocument($id);

        $positions = json_decode($_POST['positions'] ?? '[]', true);

        if (!is_array($positions) || count($positions) === 0) {
            $_SESSION['error'] = 'Tempatkan kotak TTE dulu.';
            header('Location: /Tandain/document/position/' . $doc['id']);
            exit;
        }

        $pdo = $this->db();

        try {
            $pdo->beginTransaction();

            $upd = $pdo->prepare(
                'UPDATE document_signers
                 SET page_number = ?, coord_x = ?, coord_y = ?
                 WHERE document_id = ? AND signer_id = ?'
            );

            foreach ($positions as $p) {
                $upd->execute([
                    max(1, (int)$p['page']),
                    max(0, min(1, (float)$p['x'])),
                    max(0, min(1, (float)$p['y'])),
                    $doc['id'],
                    $p['signer_id'],
                ]);
            }

            if ($doc['is_sequential']) {
                $pdo->prepare(
                    "UPDATE document_signers SET status = 'CURRENT_TURN'
                     WHERE document_id = ? AND signing_order = 1"
                )->execute([$doc['id']]);
            } else {
                $pdo->prepare(
                    "UPDATE document_signers SET status = 'CURRENT_TURN' WHERE document_id = ?"
                )->execute([$doc['id']]);
            }

            $pdo->prepare("UPDATE documents SET status = 'IN_PROGRESS' WHERE id = ?")
                ->execute([$doc['id']]);

            $pdo->prepare(
                'INSERT INTO document_audit_logs (document_id, user_id, action, notes)
                 VALUES (?, ?, ?, ?)'
            )->execute([$doc['id'], $this->userId(), 'POSITIONS_SET', 'Posisi TTE ditetapkan, dokumen dikirim']);

            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $_SESSION['error'] = 'Gagal menyimpan posisi: ' . $e->getMessage();
            header('Location: /Tandain/document/position/' . $doc['id']);
            exit;
        }

        header('Location: /Tandain/document');
        exit;
    }

    private function findOwnedDocument($id): array
    {
        if (!preg_match('/^[0-9a-f-]{36}$/i', (string)$id)) {
            http_response_code(404);
            exit('Dokumen tidak ditemukan.');
        }

        $stmt = $this->db()->prepare(
            'SELECT * FROM documents WHERE id = ? AND requester_id = ?'
        );

        $stmt->execute([$id, $this->userId()]);
        $doc = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$doc) {
            http_response_code(404);
            exit('Dokumen tidak ditemukan.');
        }

        return $doc;
    }

    public function detail()
    {
        $this->authCheck();

        $id = $_GET['id'] ?? '';
        $documentModel = $this->model('Document');
        $document = $documentModel->getDocumentByIdAndUser($id, $_SESSION['user']['id']);

        if (!$document) {
            $this->redirect('/Tandain/document');
        } else {
            $signers = $documentModel->getSignersByDocumentId($document['id']);
            $signedCount = 0;

            foreach ($signers as $signer) {
                if ($signer['status'] === 'SIGNED') {
                    $signedCount++;
                }
            }

            $totalSigners = count($signers);
            $percent = 0;

            if ($totalSigners > 0) {
                $percent = (int) (($signedCount / $totalSigners) * 100);
            }

            $data['title'] = 'Detail Dokumen - Tandain';
            $data['active_menu'] = 'uploaded';
            $data['user'] = $_SESSION['user'];
            $data['document'] = $document;
            $data['signers'] = $signers;
            $data['signedCount'] = $signedCount;
            $data['totalSigners'] = $totalSigners;
            $data['percent'] = $percent;

            $this->view('document/detail', $data);
        }
    }

    public function signed()
    {
        $this->authCheck();

        $data['title'] = 'Pesetujuan Dokumen - Tandain';
        $data['active_menu'] = 'signed';
        $data['user'] = $_SESSION['user'];

        $this->view('document/signed', $data);
    }
}