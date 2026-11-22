<?php

class VerificationController extends Controller
{
    private const MAX_SIZE = 5242880;

    public function index($documentId = null)
    {
        $data['title'] = 'Verifikasi Dokumen - Tandain';
        $data['active_menu'] = 'verification';
        $data['user'] = $_SESSION['user'] ?? null;
        $data['status'] = 'upload';
        $data['document'] = null;
        $data['file_name'] = '';
        $data['file_size'] = 0;
        $data['file_hash'] = '';
        $data['is_expired'] = false;

        if ($documentId !== null) {
            $documentId = trim((string) $documentId);

            if (!isValidUuid($documentId)) {
                $data['status'] = 'error';
                $data['file_name'] = 'ID QR Dokumen Tidak Valid';
            } else {
                $documentModel = $this->model('Document');
                $document = $documentModel->verifyDocument($documentId);

                if ($document && $this->isCompletedWithAllSignatures($document)) {
                    $isExpired = false;

                    if (!empty($document['expires_at'])) {
                        $expiryTimestamp = strtotime($document['expires_at']);
                        $isExpired = $expiryTimestamp !== false && date('Y-m-d', $expiryTimestamp) < date('Y-m-d');
                    }

                    $data['status'] = $isExpired ? 'expired' : 'success';
                    $data['document'] = $document;
                    $data['is_expired'] = $isExpired;
                    $data['file_hash'] = (string) $document['final_sha256_hash'];
                    $data['file_name'] = basename((string) $document['final_file_path']);
                } else {
                    $data['status'] = 'error';
                    $data['file_name'] = 'Dokumen ID: ' . $documentId;
                    $data['file_hash'] = '-';
                }
            }
        }

        $this->view('verification/index', $data);
    }

    public function check()
    {
        $data['title'] = 'Verifikasi Dokumen - Tandain';
        $data['active_menu'] = 'verification';
        $data['user'] = $_SESSION['user'] ?? null;
        $data['status'] = 'upload';
        $data['document'] = null;
        $data['file_name'] = '';
        $data['file_size'] = 0;
        $data['file_hash'] = '';
        $data['is_expired'] = false;

        $file = $_FILES['document_file'] ?? [];
        $uploadStatus = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        $fileSize = (int) ($file['size'] ?? 0);
        $tmpPath = $file['tmp_name'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $uploadStatus === UPLOAD_ERR_NO_FILE) {
            $data['upload_error'] = 'Pilih berkas PDF terlebih dahulu.';
        } elseif (in_array($uploadStatus, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || $fileSize > self::MAX_SIZE) {
            $data['upload_error'] = 'Ukuran berkas maksimal 5 MB.';
        } elseif ($uploadStatus !== UPLOAD_ERR_OK || !is_string($tmpPath) || !is_uploaded_file($tmpPath)) {
            $data['upload_error'] = 'Berkas gagal diunggah. Coba lagi.';
        } elseif ((new finfo(FILEINFO_MIME_TYPE))->file($tmpPath) !== 'application/pdf') {
            $data['upload_error'] = 'Berkas harus berformat PDF.';
        } else {
            $originalName = trim($file['name'] ?? 'dokumen.pdf');
            $uploadedHash = hash_file('sha256', $tmpPath);

            $data['file_name'] = $originalName;
            $data['file_size'] = $fileSize;
            $data['file_hash'] = $uploadedHash ?: '-';

            if (!$uploadedHash) {
                $data['status'] = 'error';
            } else {
                $documentModel = $this->model('Document');
                $document = $documentModel->verifyDocument($uploadedHash);
                $expectedHash = !empty($document['final_sha256_hash']) ? (string) $document['final_sha256_hash'] : '';

                $isValid = $document
                    && $this->isCompletedWithAllSignatures($document)
                    && $expectedHash !== ''
                    && hash_equals($expectedHash, $uploadedHash);

                if ($isValid) {
                    $isExpired = false;

                    if (!empty($document['expires_at'])) {
                        $expiryTimestamp = strtotime($document['expires_at']);
                        $isExpired = $expiryTimestamp !== false && date('Y-m-d', $expiryTimestamp) < date('Y-m-d');
                    }

                    $data['status'] = $isExpired ? 'expired' : 'success';
                    $data['document'] = $document;
                    $data['is_expired'] = $isExpired;
                } else {
                    $data['status'] = 'error';
                }
            }
        }

        $this->view('verification/index', $data);
    }

    public function file($documentId = null)
    {
        $documentId = trim((string) ($documentId ?? ''));
        $document = null;
        $relativePath = '';

        if (isValidUuid($documentId)) {
            $documentModel = $this->model('Document');
            $candidate = $documentModel->verifyDocument($documentId);

            if ($candidate && $this->isCompletedWithAllSignatures($candidate)) {
                $document = $candidate;
                $relativePath = ltrim((string) $candidate['final_file_path'], '/');
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
                header('Content-Disposition: attachment; filename="verified-document-' . rawurlencode($document['id']) . '.pdf"');
            }

            readfile($relativePath);
        }

        exit;
    }

    private function isCompletedWithAllSignatures($document)
    {
        $valid = false;

        if (
            is_array($document)
            && ($document['status'] ?? '') === 'COMPLETED'
            && !empty($document['final_sha256_hash'])
            && !empty($document['final_file_path'])
        ) {
            $signers = is_array($document['signers'] ?? null) ? $document['signers'] : [];

            if (count($signers) > 0) {
                $valid = true;

                foreach ($signers as $signer) {
                    if (($signer['status'] ?? '') !== 'SIGNED') {
                        $valid = false;
                    }
                }
            }
        }

        return $valid;
    }
}
