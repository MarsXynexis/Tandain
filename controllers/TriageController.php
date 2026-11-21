<?php

class TriageController extends Controller
{
    public function index()
    {
        $this->adminCheck();

        $docModel = $this->model('Document');

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? 'pending');

        if (!in_array($status, ['pending', 'dispatched', 'rejected', 'all'], true)) {
            $status = 'pending';
        }

        $data['title'] = 'Disposisi Dokumen - Tandain';
        $data['active_menu'] = 'triage';
        $data['user'] = $_SESSION['user'];
        $data['search'] = $search;
        $data['current_status'] = $status;
        $data['documents'] = $docModel->getPendingTriageDocuments($status, $search);
        $data['stats'] = $docModel->getTriageStatistics();

        $this->view('triage/index', $data);
    }

    public function review($id = null)
    {
        $this->adminCheck();

        $docModel = $this->model('Document');
        $signerModel = $this->model('DocumentSigner');

        $doc = !empty($id) ? $docModel->findByIdWithRequester($id) : null;

        if (empty($doc)) {
            $this->setFlash('error', 'Dokumen yang ingin ditelaah tidak ditemukan.');

            $this->redirect('/Tandain/triage');
        } else {
            $data['title'] = 'Telaah Disposisi Dokumen - Tandain';
            $data['active_menu'] = 'triage_review';
            $data['user'] = $_SESSION['user'];
            $data['document'] = $doc;
            $data['current_signers'] = $signerModel->getSignerByDocumentId($id);
            $data['can_dispose'] = ($doc['status'] === 'PENDING_TRIAGE');

            $this->view('triage/review', $data);
        }
    }

    public function file($id = null)
    {
        $this->adminCheck();

        $docModel = $this->model('Document');
        $doc = !empty($id) ? $docModel->findById($id) : null;
        $version = $_GET['version'] ?? 'latest';
        $relativePath = '';

        if ($doc) {
            if ($version !== 'original' && $doc['status'] === 'COMPLETED' && !empty($doc['final_file_path'])) {
                $relativePath = ltrim((string) $doc['final_file_path'], '/');
            } else {
                $relativePath = ltrim((string) ($doc['original_file_path'] ?? ''), '/');
            }
        }

        if (empty($doc) || !preg_match('/\Auploads\/(?:documents|signed)\/[A-Za-z0-9._-]+\.pdf\z/i', $relativePath) || !is_file($relativePath)) {
            http_response_code(404);
        } else {
            header('Content-Type: application/pdf');
            header('Content-Length: ' . filesize($relativePath));
            header('X-Content-Type-Options: nosniff');

            readfile($relativePath);
        }

        exit;
    }

    public function searchSigners()
    {
        $this->authCheck();

        $keyword = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
        $length = mb_strlen($keyword);
        $users = [];

        if ($length >= 2 && $length <= 100) {
            $userModel = $this->model('User');
            $users = $userModel->searchSignerCandidates($keyword);
        }

        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');

        echo json_encode($users);
    }

    public function processDisposition()
    {
        $this->adminCheck();

        $redirectUrl = '/Tandain/triage';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $documentId = trim($_POST['document_id'] ?? '');
            $documentNumber = trim($_POST['document_number'] ?? '');
            $expiresAt = trim($_POST['expires_at'] ?? '');
            $isSequential = isset($_POST['is_sequential']) && $_POST['is_sequential'] === '1';
            $signerIds = is_array($_POST['signer_ids'] ?? null) ? $_POST['signer_ids'] : [];

            $docModel = $this->model('Document');
            $userModel = $this->model('User');
            $doc = !empty($documentId) ? $docModel->findById($documentId) : null;

            $signersList = [];
            $assignedSignerIds = [];

            foreach ($signerIds as $signerId) {
                if (!empty($signerId) && !in_array($signerId, $assignedSignerIds, true)) {
                    $assignedSignerIds[] = $signerId;
                    $signersList[] = ['signer_id' => $signerId];
                }
            }

            if (empty($doc)) {
                $this->setFlash('error', 'Dokumen yang ingin didisposisikan tidak ditemukan.');
            } elseif ($doc['status'] !== 'PENDING_TRIAGE') {
                $this->setFlash('error', 'Dokumen ini sudah tidak berada pada antrean disposisi.');
            } else {
                $redirectUrl = '/Tandain/triage/review/' . rawurlencode($documentId);

                if (empty($signersList)) {
                    $this->setFlash('error', 'Tetapkan minimal satu penandatangan sebelum menyimpan disposisi.');
                } elseif (count($userModel->getAssignableSignersByIds($assignedSignerIds)) !== count($assignedSignerIds)) {
                    $this->setFlash('error', 'Pilih akun civitas aktif yang memiliki sertifikat TTE dan bukan Admin TU.');
                } elseif (!empty($expiresAt) && !$this->isValidDate($expiresAt)) {
                    $this->setFlash('error', 'Batas kedaluwarsa dokumen tidak valid.');
                } elseif (!empty($expiresAt) && $expiresAt < date('Y-m-d')) {
                    $this->setFlash('error', 'Batas kedaluwarsa dokumen tidak boleh berada di masa lalu.');
                } elseif (!empty($documentNumber) && !empty($docModel->isDocumentNumberTaken($documentNumber, $documentId))) {
                    $this->setFlash('error', 'Nomor naskah kedinasan sudah digunakan oleh dokumen lain.');
                } else {
                    $success = $docModel->applyTriageDisposition($documentId, $_SESSION['user']['id'], $documentNumber, $signersList, $isSequential, $expiresAt);

                    if ($success) {
                        $redirectUrl = '/Tandain/triage/position/' . rawurlencode($documentId);
                    } else {
                        $this->setFlash('error', 'Disposisi gagal disimpan. Periksa kembali data penandatangan.');
                    }
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    public function position($id = null)
    {
        $this->adminCheck();

        $docModel = $this->model('Document');
        $doc = !empty($id) ? $docModel->findById($id) : null;
        $signers = !empty($doc) ? $docModel->getSignersForPositioning($doc['id']) : [];

        if (empty($doc) || $doc['status'] !== 'PENDING_TRIAGE') {
            $this->redirect('/Tandain/triage');
        } elseif (empty($signers)) {
            $this->redirect('/Tandain/triage/review/' . rawurlencode($doc['id']));
        } else {
            $data['title'] = 'Atur Posisi TTE Disposisi - Tandain';
            $data['active_menu'] = 'triage';
            $data['user'] = $_SESSION['user'];
            $data['documentId'] = $doc['id'];
            $data['signers'] = $signers;
            $data['pdfUrl'] = '/Tandain/triage/file/' . rawurlencode($doc['id']);

            $this->view('triage/position', $data);
        }
    }

    public function savePositions($id = null)
    {
        $this->adminCheck();

        $docModel = $this->model('Document');
        $doc = !empty($id) ? $docModel->findById($id) : null;
        $redirectUrl = '/Tandain/triage';

        if (!empty($doc) && $doc['status'] === 'PENDING_TRIAGE') {
            $redirectUrl = '/Tandain/triage/position/' . rawurlencode($doc['id']);
            $positions = json_decode($_POST['positions'] ?? '[]', true);
            $isSequential = in_array($doc['is_sequential'], [true, 1, '1', 't', 'true'], true);

            if (!is_array($positions) || count($positions) === 0) {
                $this->setFlash('error', 'Tempatkan kotak TTE dulu.');
            } elseif ($docModel->updateSignerPositions($doc['id'], $positions, $isSequential, $_SESSION['user']['id'], 'PENDING_TRIAGE')) {
                $this->setFlash('success', 'Disposisi dokumen berhasil disimpan dan diteruskan ke penandatangan pertama.');

                $redirectUrl = '/Tandain/triage?status=dispatched';
            } else {
                $this->setFlash('error', 'Gagal menyimpan posisi tanda tangan.');
            }
        }

        $this->redirect($redirectUrl);
    }

    public function reject()
    {
        $this->adminCheck();

        $redirectUrl = '/Tandain/triage';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $documentId = trim($_POST['document_id'] ?? '');
            $reason = trim($_POST['rejection_reason'] ?? '');

            $docModel = $this->model('Document');
            $doc = !empty($documentId) ? $docModel->findById($documentId) : null;

            if (empty($doc)) {
                $this->setFlash('error', 'Dokumen yang ingin ditolak tidak ditemukan.');
            } elseif ($doc['status'] !== 'PENDING_TRIAGE') {
                $this->setFlash('error', 'Dokumen ini sudah tidak berada pada antrean disposisi.');
            } elseif (empty($reason)) {
                $this->setFlash('error', 'Alasan penolakan wajib diisi.');
            } else {
                $success = $docModel->rejectTriageDisposition($documentId, $_SESSION['user']['id'], $reason);

                if ($success) {
                    $this->setFlash('success', 'Pengajuan dokumen ditolak dan alasan telah dicatat pada log audit.');

                    $redirectUrl = '/Tandain/triage?status=rejected';
                } else {
                    $this->setFlash('error', 'Penolakan dokumen gagal disimpan.');
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    private function isValidDate($date)
    {
        $parsedDate = DateTime::createFromFormat('Y-m-d', $date);

        return $parsedDate && $parsedDate->format('Y-m-d') === $date;
    }
}
