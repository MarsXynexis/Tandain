<?php

class InboxController extends Controller
{
    public function index()
    {
        $this->authCheck();

        $data['title'] = 'Persetujuan Dokumen - Tandain';
        $data['active_menu'] = 'signed';
        $data['user'] = $_SESSION['user'];


        $documentSignerModel = $this->model('DocumentSigner');

        $data['documents'] = $documentSignerModel->getDocumentsBySignerId($data['user']['id']);

        $this->view('inbox/index', $data);
    }

    public function detail()
    {
        $this->authCheck();

        $data['title'] = 'Persetujuan Dokumen - Tandain';
        $data['active_menu'] = 'signed';
        $data['user'] = $_SESSION['user'];

        if (!isset($_SESSION['inbox_stamp_csrf_token']) || !is_string($_SESSION['inbox_stamp_csrf_token'])) {
            $_SESSION['inbox_stamp_csrf_token'] = bin2hex(random_bytes(32));
        }

        $documentSignerModel = $this->model('DocumentSigner');
        $documentId = trim($_GET['document_id'] ?? '');

        if ($documentId === '') {
            header('Location: /Tandain/inbox');
            exit;
        }

        $data['document'] = $documentSignerModel->getDetailDocumentById($documentId, $data['user']['id']);

        if (!$data['document']) {
            header('Location: /Tandain/inbox');
            exit;
        }

        $data['signers'] = $documentSignerModel->getSignerByDocumentId($documentId);
        $data['stamp_csrf_token'] = $_SESSION['inbox_stamp_csrf_token'];

        $this->view('inbox/detail', $data);
    }

    public function updateStampType()
    {
        $this->authCheck();

        $documentIdInput = $_POST['document_id'] ?? '';
        $documentId = is_string($documentIdInput) ? trim($documentIdInput) : '';
        $stampType = $_POST['stamp_type'] ?? '';
        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['inbox_stamp_csrf_token'] ?? '';

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
            || $documentId === ''
            || !in_array($stampType, ['SIGNATURE_ONLY', 'DUAL_DISPLAY'], true)
            || !is_string($csrfToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $csrfToken)
        ) {
            $this->setFlash('error', 'Pilihan tanda tangan tidak valid. Silakan coba lagi.');
            $this->redirect('/Tandain/inbox');
        }

        $documentSignerModel = $this->model('DocumentSigner');
        $document = $documentSignerModel->getDetailDocumentById($documentId, $_SESSION['user']['id']);

        if (!$document || $document['signer_status'] !== 'CURRENT_TURN') {
            $this->setFlash('error', 'Pilihan tanda tangan hanya dapat diubah saat dokumen menunggu giliran Anda.');
            $this->redirect('/Tandain/inbox');
        }

        if ($document['signer_stamp_type'] === $stampType) {
            $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
        }

        if (!$documentSignerModel->updateStampType($documentId, $_SESSION['user']['id'], $stampType)) {
            $this->setFlash('error', 'Gagal menyimpan pilihan tanda tangan. Silakan coba lagi.');
            $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
        }

        $this->setFlash('success', 'Pilihan tanda tangan berhasil diperbarui.');
        $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
    }

    public function agree()
    {
        $this->authCheck();

        $documentIdInput = $_POST['document_id'] ?? '';
        $documentId = is_string($documentIdInput) ? trim($documentIdInput) : '';
        $passphrase = $_POST['passphrase'] ?? '';
        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['inbox_stamp_csrf_token'] ?? '';

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
            || $documentId === ''
            || !is_string($passphrase)
            || !is_string($csrfToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $csrfToken)
        ) {
            $this->setFlash('error', 'Permintaan tidak valid. Silakan coba lagi.');
            $this->redirect('/Tandain/inbox');
        }

        $documentSignerModel = $this->model('DocumentSigner');
        try {
            $result = $documentSignerModel->agreeDocument($documentId, $_SESSION['user']['id'], $passphrase);
        } catch (Throwable $exception) {
            error_log('Inbox signature failed for document ' . $documentId . ': ' . $exception->getMessage());
            $this->setFlash('error', 'Terjadi kesalahan saat menyetujui dokumen. Silakan coba lagi.');
            $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
        }

        if (!$result['ok']) {
            $this->setFlash('error', $result['message']);
            $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
        }

        $this->setFlash(
            'success',
            $result['completed']
                ? 'Tanda tangan digital berhasil dibubuhkan. Proses dokumen telah selesai.'
                : 'Tanda tangan digital berhasil dibubuhkan. Dokumen diteruskan ke penandatangan berikutnya.'
        );
        $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
    }

}
