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
            $data['current_signers'] = $signerModel->getByDocumentId($id);
            $data['can_dispose'] = ($doc['status'] === 'PENDING_TRIAGE');

            $this->view('triage/review', $data);
        }
    }

    public function searchSigners()
    {
        $this->adminCheck();

        $keyword = trim($_GET['q'] ?? '');
        $users = [];

        if (strlen($keyword) >= 2) {
            $userModel = $this->model('User');
            $users = $userModel->searchActiveUsers($keyword, 8);
        }

        header('Content-Type: application/json; charset=utf-8');

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
            $stampTypes = is_array($_POST['stamp_types'] ?? null) ? $_POST['stamp_types'] : [];
            $pageNumbers = is_array($_POST['page_numbers'] ?? null) ? $_POST['page_numbers'] : [];

            $docModel = $this->model('Document');
            $userModel = $this->model('User');
            $doc = !empty($documentId) ? $docModel->findById($documentId) : null;

            $signersList = [];
            $assignedSignerIds = [];

            foreach ($signerIds as $index => $signerId) {
                if (!empty($signerId) && !in_array($signerId, $assignedSignerIds, true)) {
                    $assignedSignerIds[] = $signerId;

                    $stampType = $stampTypes[$index] ?? 'DUAL_DISPLAY';

                    if (!in_array($stampType, ['DUAL_DISPLAY', 'SIGNATURE_ONLY', 'QR_ONLY'], true)) {
                        $stampType = 'DUAL_DISPLAY';
                    }

                    $pageNumber = !empty($pageNumbers[$index]) ? (int) $pageNumbers[$index] : 1;

                    if ($pageNumber < 1) {
                        $pageNumber = 1;
                    }

                    $signersList[] = [
                        'signer_id' => $signerId,
                        'stamp_type' => $stampType,
                        'page_number' => $pageNumber,
                        'coord_x' => 120,
                        'coord_y' => 650,
                    ];
                }
            }

            if (empty($doc)) {
                $this->setFlash('error', 'Dokumen yang ingin didisposisikan tidak ditemukan.');
            } elseif ($doc['status'] !== 'PENDING_TRIAGE') {
                $this->setFlash('error', 'Dokumen ini sudah tidak berada pada antrean disposisi.');
            } elseif (empty($documentNumber)) {
                $this->setFlash('error', 'Nomor naskah kedinasan wajib diisi.');
            } elseif (empty($signersList)) {
                $this->setFlash('error', 'Tetapkan minimal satu penandatangan sebelum menyimpan disposisi.');
            } elseif (count($userModel->getAssignableSignersByIds($assignedSignerIds)) !== count($assignedSignerIds)) {
                $this->setFlash('error', 'Penandatangan yang dipilih harus berstatus aktif dan memiliki sertifikat TTE.');
            } elseif (!empty($expiresAt) && !$this->isValidDate($expiresAt)) {
                $this->setFlash('error', 'Batas kedaluwarsa dokumen tidak valid.');
            } elseif (!empty($expiresAt) && $expiresAt < date('Y-m-d')) {
                $this->setFlash('error', 'Batas kedaluwarsa dokumen tidak boleh berada di masa lalu.');
            } elseif (!empty($docModel->isDocumentNumberTaken($documentNumber, $documentId))) {
                $this->setFlash('error', 'Nomor naskah kedinasan sudah digunakan oleh dokumen lain.');
            } else {
                $success = $docModel->applyTriageDisposition($documentId, $_SESSION['user']['id'], $documentNumber, $signersList, $isSequential, $expiresAt);

                if ($success) {
                    $this->setFlash('success', 'Disposisi dokumen berhasil disimpan dan diteruskan ke penandatangan pertama.');

                    $redirectUrl = '/Tandain/triage?status=dispatched';
                } else {
                    $this->setFlash('error', 'Disposisi gagal disimpan. Periksa kembali data penandatangan.');
                }
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
