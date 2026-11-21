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

        if (($_GET['download'] ?? '') === 'signed') {
            $finalFilePath = $data['document']['final_file_path'] ?? '';
            if (
                $data['document']['status'] !== 'COMPLETED'
                || !is_string($finalFilePath)
                || !preg_match('/\Auploads\/(?:documents|signed)\/[A-Za-z0-9._-]+\.pdf\z/i', $finalFilePath)
            ) {
                $this->setFlash('error', 'Berkas Signed belum tersedia.');
                $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
            }

            $finalFile = $finalFilePath;
            if (!is_file($finalFile) || !is_readable($finalFile)) {
                error_log('Signed document file is missing or unreadable for document ' . $documentId);
                $this->setFlash('error', 'Berkas Signed tidak dapat diunduh saat ini.');
                $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
            }

            header('Content-Type: application/pdf');
            header('Content-Length: ' . filesize($finalFile));
            header('Content-Disposition: attachment; filename="signed-document-' . $documentId . '.pdf"');
            header('X-Content-Type-Options: nosniff');
            readfile($finalFile);
            exit;
        }

        $data['signers'] = $documentSignerModel->getSignerByDocumentId($documentId);
        foreach ($data['signers'] as &$signer) {
            if ($signer['signer_id'] !== $data['user']['id']) {
                continue;
            }

            $signaturePath = $signer['signer_signature_image_path'] ?? '';
            $signatureFile = '';

            if (
                is_string($signaturePath)
                && preg_match('/\Auploads\/signatures\/[A-Za-z0-9._-]+\.png\z/i', $signaturePath)
            ) {
                $signatureFile = dirname(__DIR__) . DIRECTORY_SEPARATOR
                    . str_replace('/', DIRECTORY_SEPARATOR, $signaturePath);
            }

            $signer['has_active_signature'] = $signatureFile !== '' && is_file($signatureFile);
        }
        unset($signer);

        if (
            $data['document']['signer_status'] === 'CURRENT_TURN'
            && $data['document']['status'] === 'IN_PROGRESS'
        ) {
            $currentSigner = null;
            foreach ($data['signers'] as $signer) {
                if ($signer['signer_id'] === $data['user']['id']) {
                    $currentSigner = $signer;
                    break;
                }
            }

            $hasActiveCertificate = $currentSigner !== null && (int) ($currentSigner['has_active_cert'] ?? 0) === 1;
            $hasActiveSignature = $currentSigner !== null && !empty($currentSigner['has_active_signature']);

            if (!$hasActiveCertificate || !$hasActiveSignature) {
                $missingItems = [];
                if (!$hasActiveCertificate) {
                    $missingItems[] = 'sertifikat TTE aktif';
                }
                if (!$hasActiveSignature) {
                    $missingItems[] = 'gambar tanda tangan aktif';
                }

                $data['signing_warning'] = 'Untuk menyetujui dokumen, lengkapi terlebih dahulu ' . implode(' dan ', $missingItems) . '.';
            }
        }

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
            || !in_array($stampType, ['SIGNATURE_ONLY', 'DUAL_DISPLAY', 'QR_ONLY'], true)
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
        $this->redirect('/Tandain/inbox/');
    }

    public function reject()
    {
        $this->authCheck();

        $documentIdInput = $_POST['document_id'] ?? '';
        $documentId = is_string($documentIdInput) ? trim($documentIdInput) : '';
        $reasonInput = $_POST['rejection_reason'] ?? '';
        $reason = is_string($reasonInput) ? trim($reasonInput) : '';
        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['inbox_stamp_csrf_token'] ?? '';

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
            || $documentId === ''
            || !is_string($csrfToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $csrfToken)
        ) {
            $this->setFlash('error', 'Permintaan tidak valid. Silakan coba lagi.');
            $this->redirect('/Tandain/inbox');
        }

        if ($reason === '' || strlen($reason) > 1000) {
            $this->setFlash('error', 'Alasan penolakan wajib diisi dan maksimal 1000 karakter.');
            $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
        }

        $documentSignerModel = $this->model('DocumentSigner');
        try {
            $result = $documentSignerModel->rejectDocument($documentId, $_SESSION['user']['id'], $reason);
        } catch (Throwable $exception) {
            error_log('Inbox rejection failed for document ' . $documentId . ': ' . $exception->getMessage());
            $this->setFlash('error', 'Terjadi kesalahan saat menolak dokumen. Silakan coba lagi.');
            $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
        }

        if (!$result['ok']) {
            $this->setFlash('error', $result['message']);
            $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
        }

        $this->setFlash('success', 'Dokumen berhasil ditolak dan alasan telah dicatat.');
        $this->redirect('/Tandain/inbox/detail?document_id=' . rawurlencode($documentId));
    }

    public function file($documentId = null)
    {
        $this->authCheck();

        $documentId = trim((string) ($documentId ?? ''));
        $userId = $_SESSION['user']['id'];
        $documentSignerModel = $this->model('DocumentSigner');
        $document = ($documentId !== '') ? $documentSignerModel->getDetailDocumentById($documentId, $userId) : null;
        $version = $_GET['version'] ?? 'latest';
        $relativePath = '';

        if ($document) {
            if ($version === 'original') {
                if (($document['requester_id'] ?? null) === $userId || empty($_GET['download'])) {
                    $relativePath = ltrim((string) ($document['original_file_path'] ?? ''), '/');
                }
            } elseif (!empty($document['final_file_path'])) {
                $relativePath = ltrim((string) $document['final_file_path'], '/');
            } else {
                $relativePath = ltrim((string) ($document['original_file_path'] ?? ''), '/');
            }
        }

        if (
            !$document
            || !preg_match('/\Auploads\/(?:documents|signed)\/[A-Za-z0-9._-]+\.pdf\z/i', $relativePath)
            || !is_file($relativePath)
            || !is_readable($relativePath)
        ) {
            http_response_code(404);
        } else {
            header('Content-Type: application/pdf');
            header('Content-Length: ' . filesize($relativePath));
            header('X-Content-Type-Options: nosniff');

            if (!empty($_GET['download'])) {
                $prefix = ($version === 'original') ? 'original-' : 'signed-document-';
                header('Content-Disposition: attachment; filename="' . $prefix . rawurlencode($document['id']) . '.pdf"');
            }

            readfile($relativePath);
        }

        exit;
    }

}
