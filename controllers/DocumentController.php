<?php

class DocumentController extends Controller
{
    private const MAX_SIZE = 2097152;

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

        $userModel = $this->model('User');

        $data['title'] = 'Ajukan Dokumen - Tandain';
        $data['active_menu'] = 'apply';
        $data['user'] = $_SESSION['user'];
        $data['candidates'] = $userModel->getSignerCandidates();
        $data['error'] = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('document/create', $data);
    }

    private function store()
    {
        if (($_POST['submission_mode'] ?? 'upload') !== 'upload') {
            $_SESSION['error'] = 'Mode template surat belum tersedia.';

            $this->redirect('/Tandain/document/create');
        }

        $title = trim($_POST['title'] ?? '');
        $expiresAt = trim($_POST['expires_at'] ?? '') ?: null;
        $isTriage = !empty($_POST['is_triage']);
        $sequential = !empty($_POST['is_sequential']);
        $signerIds = array_values(array_unique($_POST['signers'] ?? []));

        if ($title === '') {
            $_SESSION['error'] = 'Judul dokumen wajib diisi.';

            $this->redirect('/Tandain/document/create');
        }

        if (!$isTriage && count($signerIds) === 0) {
            $_SESSION['error'] = 'Pilih minimal satu penandatangan.';

            $this->redirect('/Tandain/document/create');
        }

        $file = $_FILES['document_file'] ?? null;

        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Berkas PDF wajib diunggah.';

            $this->redirect('/Tandain/document/create');
        }

        if ($file['size'] > self::MAX_SIZE) {
            $_SESSION['error'] = 'Ukuran berkas maksimal 2 MB.';

            $this->redirect('/Tandain/document/create');
        }

        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

        if ($mime !== 'application/pdf') {
            $_SESSION['error'] = 'Berkas harus berformat PDF.';

            $this->redirect('/Tandain/document/create');
        }

        $dir = dirname(__DIR__) . '/uploads/documents/';

        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.pdf';

        if (!move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            $_SESSION['error'] = 'Gagal menyimpan berkas.';

            $this->redirect('/Tandain/document/create');
        }

        $hash = hash_file('sha256', $dir . $filename);
        $documentModel = $this->model('Document');
        $userId = $_SESSION['user']['id'];

        $docData = [
            'title' => $title,
            'requester_id' => $userId,
            'original_file_path' => 'uploads/documents/' . $filename,
            'initial_sha256_hash' => $hash,
            'status' => $isTriage ? 'PENDING_TRIAGE' : 'DRAFT',
            'routing_type' => $isTriage ? 'TRIAGE_ASSISTED' : 'DIRECT',
            'is_sequential' => $sequential,
            'expires_at' => $expiresAt
        ];

        $docId = $documentModel->createDocument($docData, $isTriage ? [] : $signerIds);

        if (!$docId) {
            @unlink($dir . $filename);
            $_SESSION['error'] = 'Gagal menyimpan dokumen ke sistem.';

            $this->redirect('/Tandain/document/create');
        }

        if ($isTriage) {
            $this->redirect('/Tandain/document');
        } else {
            $this->redirect('/Tandain/document/position/' . $docId);
        }
    }

    public function position($id = null)
    {
        $this->authCheck();

        $userId = $_SESSION['user']['id'];
        $documentModel = $this->model('Document');
        $doc = $documentModel->getDocumentByIdAndUser($id, $userId);

        if (!$doc) {
            $this->redirect('/Tandain/document');
        }

        $data['title'] = 'Atur Posisi TTE - Tandain';
        $data['active_menu'] = 'apply';
        $data['user'] = $_SESSION['user'];
        $data['documentId'] = $doc['id'];
        $data['signers'] = $documentModel->getSignersForPositioning($doc['id']);
        $data['pdfUrl'] = '/Tandain/document/file/' . $doc['id'];
        $data['error'] = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);

        $this->view('document/position', $data);
    }

    public function file($id = null)
    {
        $this->authCheck();

        $userId = $_SESSION['user']['id'];
        $documentModel = $this->model('Document');
        $doc = $documentModel->getDocumentByIdAndUser($id, $userId);

        if (!$doc) {
            $this->redirect('/Tandain/document');
        }

        $path = dirname(__DIR__) . '/' . $doc['original_file_path'];

        if (!is_file($path)) {
            $this->redirect('/Tandain/document');
        }

        header('Content-Type: application/pdf');
        header('Content-Length: ' . filesize($path));

        readfile($path);

        exit;
    }

    public function savePositions($id = null)
    {
        $this->authCheck();

        $userId = $_SESSION['user']['id'];
        $documentModel = $this->model('Document');
        $doc = $documentModel->getDocumentByIdAndUser($id, $userId);

        if (!$doc) {
            $this->redirect('/Tandain/document');
        }

        $positions = json_decode($_POST['positions'] ?? '[]', true);

        if (!is_array($positions) || count($positions) === 0) {
            $_SESSION['error'] = 'Tempatkan kotak TTE dulu.';

            $this->redirect('/Tandain/document/position/' . $doc['id']);
        }

        $success = $documentModel->updateSignerPositions($doc['id'], $positions, $doc['is_sequential'], $userId);

        if (!$success) {
            $_SESSION['error'] = 'Gagal menyimpan posisi tanda tangan.';

            $this->redirect('/Tandain/document/position/' . $doc['id']);
        }

        $this->redirect('/Tandain/document');
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
