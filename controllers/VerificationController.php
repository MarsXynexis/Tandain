<?php

class VerificationController extends Controller
{
    private const MAX_SIZE = 5242880;

    public function index($documentId = null)
    {
        if (isset($_SESSION['user'])) {
            $this->refreshAuthenticatedUser();
        }

        $data['title'] = 'Verifikasi Dokumen - Tandain';
        $data['active_menu'] = 'verification';
        $data['user'] = $_SESSION['user'] ?? null;
        $data['status'] = 'upload';
        $data['document'] = null;
        $data['file_name'] = '';
        $data['file_size'] = 0;
        $data['file_hash'] = '';
        $data['is_expired'] = false;
        $data['server_file_available'] = false;
        $data['warning_title'] = '';
        $data['warning_message'] = '';
        $data['error_title'] = '';
        $data['error_message'] = '';

        if ($documentId !== null) {
            $documentId = trim((string) $documentId);

            if (!isValidUuid($documentId)) {
                $data['status'] = 'error';
                $data['file_name'] = 'ID QR Dokumen Tidak Valid';
            } else {
                $documentModel = $this->model('Document');
                $document = $documentModel->verifyDocument($documentId);

                if ($document && $this->isCompletedWithAllSignatures($document)) {
                    $fileCheck = $this->verifyFinalPdfFile($document);

                    if (!$fileCheck['ok']) {
                        $data['status'] = 'error';
                        $data['error_title'] = $fileCheck['error_title'];
                        $data['error_message'] = $fileCheck['error_message'];
                        $data['file_name'] = basename((string) $document['final_file_path']);
                        $data['file_hash'] = '-';
                    } else {
                        $verification = $this->evaluateDocumentVerification($document);

                        $data['status'] = $verification['status'];
                        $data['document'] = $verification['document'];
                        $data['is_expired'] = $verification['is_expired'];
                        $data['server_file_available'] = ($verification['status'] !== 'error');
                        $data['warning_title'] = $verification['warning_title'];
                        $data['warning_message'] = $verification['warning_message'];
                        $data['error_title'] = $verification['error_title'];
                        $data['error_message'] = $verification['error_message'];
                        $data['file_hash'] = (string) $document['final_sha256_hash'];
                        $data['file_name'] = basename((string) $document['final_file_path']);
                    }
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
        if (isset($_SESSION['user'])) {
            $this->refreshAuthenticatedUser();
        }

        $data['title'] = 'Verifikasi Dokumen - Tandain';
        $data['active_menu'] = 'verification';
        $data['user'] = $_SESSION['user'] ?? null;
        $data['status'] = 'upload';
        $data['document'] = null;
        $data['file_name'] = '';
        $data['file_size'] = 0;
        $data['file_hash'] = '';
        $data['is_expired'] = false;
        $data['server_file_available'] = false;
        $data['warning_title'] = '';
        $data['warning_message'] = '';
        $data['error_title'] = '';
        $data['error_message'] = '';

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
            $magicBytes = '';
            $handle = @fopen($tmpPath, 'rb');

            if ($handle !== false) {
                $magicBytes = (string) fread($handle, 5);
                fclose($handle);
            }

            if ($magicBytes !== '%PDF-') {
                $data['upload_error'] = 'Berkas harus berformat PDF yang valid.';
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
                        $verification = $this->evaluateDocumentVerification($document);
                        $serverFileCheck = $this->verifyFinalPdfFile($document);

                        $data['status'] = $verification['status'];
                        $data['document'] = $verification['document'];
                        $data['is_expired'] = $verification['is_expired'];
                        $data['server_file_available'] = ($verification['status'] !== 'error' && $serverFileCheck['ok']);
                        $data['warning_title'] = $verification['warning_title'];
                        $data['warning_message'] = $verification['warning_message'];
                        $data['error_title'] = $verification['error_title'];
                        $data['error_message'] = $verification['error_message'];
                    } else {
                        $data['status'] = 'error';
                    }
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
                $fileCheck = $this->verifyFinalPdfFile($candidate);

                if ($fileCheck['ok']) {
                    $verification = $this->evaluateDocumentVerification($candidate);

                    if ($verification['status'] !== 'error') {
                        $document = $verification['document'];
                        $relativePath = $fileCheck['path'];
                    }
                }
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

    private function verifyFinalPdfFile(array $document): array
    {
        $relativePath = ltrim((string) ($document['final_file_path'] ?? ''), '/');
        $expectedHash = (string) ($document['final_sha256_hash'] ?? '');
        $ok = false;
        $errorTitle = '';
        $errorMessage = '';

        if (
            !preg_match('/\Auploads\/(?:documents|signed)\/[A-Za-z0-9._-]+\.pdf\z/i', $relativePath)
            || !is_file($relativePath)
            || !is_readable($relativePath)
        ) {
            $errorTitle = 'Berkas Final Tidak Ditemukan';
            $errorMessage = 'Berkas PDF bertanda tangan pada server tidak tersedia atau tidak dapat dibaca.';
        } else {
            $actualHash = hash_file('sha256', $relativePath);

            if ($expectedHash === '' || !is_string($actualHash) || !hash_equals($expectedHash, $actualHash)) {
                $errorTitle = 'Integritas Berkas Final Gagal';
                $errorMessage = 'Berkas PDF bertanda tangan pada server tidak cocok dengan hash SHA-256 yang tercatat.';
            } else {
                $ok = true;
            }
        }

        return [
            'ok' => $ok,
            'path' => $relativePath,
            'error_title' => $errorTitle,
            'error_message' => $errorMessage,
        ];
    }

    private function verifySignerCrypto(array $document, array $signer): bool
    {
        $verified = false;
        $signatureJson = (string) ($signer['signature_value'] ?? '');
        $publicKeyPem = (string) ($signer['public_key'] ?? '');

        if (
            function_exists('openssl_pkey_get_public')
            && function_exists('openssl_verify')
            && $signatureJson !== ''
            && $publicKeyPem !== ''
        ) {
            $envelope = json_decode($signatureJson, true);

            if (
                is_array($envelope)
                && ($envelope['algorithm'] ?? '') === 'RSA-SHA256'
                && is_string($envelope['payload'] ?? null)
                && is_string($envelope['signature'] ?? null)
            ) {
                $decodedPayload = base64_decode($envelope['payload'], true);
                $decodedSignature = base64_decode($envelope['signature'], true);

                if (is_string($decodedPayload) && $decodedPayload !== '' && is_string($decodedSignature) && $decodedSignature !== '') {
                    $payloadData = json_decode($decodedPayload, true);
                    $expectedSerial = (string) ($signer['certificate_serial'] ?? '');
                    $expectedSignedHash = (string) ($signer['signed_hash'] ?? '');
                    $expectedInitialHash = (string) ($document['initial_sha256_hash'] ?? '');
                    $payloadDocHash = is_array($payloadData) ? (string) ($payloadData['document_sha256'] ?? '') : '';
                    $serialMatched = $expectedSerial === '' || ($payloadData['certificate_serial'] ?? '') === $expectedSerial;
                    $hashMatched = $expectedSignedHash === '' || hash_equals($expectedSignedHash, hash('sha256', $decodedPayload));
                    $initialHashMatched = $expectedInitialHash !== ''
                        && $payloadDocHash !== ''
                        && hash_equals($expectedInitialHash, $payloadDocHash);

                    if (
                        is_array($payloadData)
                        && ($payloadData['document_id'] ?? '') === ($document['id'] ?? '')
                        && ($payloadData['signer_id'] ?? '') === ($signer['signer_id'] ?? '')
                        && $serialMatched
                        && $hashMatched
                        && $initialHashMatched
                    ) {
                        $publicKey = openssl_pkey_get_public($publicKeyPem);

                        if ($publicKey !== false && openssl_verify($decodedPayload, $decodedSignature, $publicKey, OPENSSL_ALGO_SHA256) === 1) {
                            $verified = true;
                        }
                    }
                }
            }
        }

        return $verified;
    }

    private function evaluateDocumentVerification(array $document): array
    {
        $signers = is_array($document['signers'] ?? null) ? $document['signers'] : [];
        $enrichedSigners = [];
        $hasInvalidSignature = false;
        $hasRevokedCert = false;
        $hasExpiredCert = false;
        $hasNotYetValidCert = false;
        $now = time();

        foreach ($signers as $signer) {
            $rsaVerified = $this->verifySignerCrypto($document, $signer);
            $rawCertStatus = (string) ($signer['cert_status'] ?? '');
            $validFromTimestamp = !empty($signer['valid_from']) ? strtotime((string) $signer['valid_from']) : false;
            $validUntilTimestamp = !empty($signer['valid_until']) ? strtotime((string) $signer['valid_until']) : false;

            if ($rawCertStatus === 'REVOKED') {
                $certEffectiveStatus = 'REVOKED';
            } elseif ($rawCertStatus === 'EXPIRED' || ($validUntilTimestamp !== false && $validUntilTimestamp < $now)) {
                $certEffectiveStatus = 'EXPIRED';
            } elseif ($rawCertStatus === 'ACTIVE' && $validFromTimestamp !== false && $validFromTimestamp > $now) {
                $certEffectiveStatus = 'NOT_YET_VALID';
            } elseif ($rawCertStatus === 'ACTIVE') {
                $certEffectiveStatus = 'ACTIVE';
            } else {
                $certEffectiveStatus = 'UNKNOWN';
            }

            if (!$rsaVerified) {
                $hasInvalidSignature = true;
            }

            if ($certEffectiveStatus === 'REVOKED') {
                $hasRevokedCert = true;
            } elseif ($certEffectiveStatus === 'EXPIRED') {
                $hasExpiredCert = true;
            } elseif ($certEffectiveStatus === 'NOT_YET_VALID') {
                $hasNotYetValidCert = true;
            }

            $signer['rsa_verified'] = $rsaVerified;
            $signer['cert_effective_status'] = $certEffectiveStatus;
            $enrichedSigners[] = $signer;
        }

        $document['signers'] = $enrichedSigners;
        $isDocExpired = false;

        if (!empty($document['expires_at'])) {
            $expiryTimestamp = strtotime((string) $document['expires_at']);
            $isDocExpired = $expiryTimestamp !== false && date('Y-m-d', $expiryTimestamp) < date('Y-m-d');
        }

        $status = 'success';
        $warningTitle = '';
        $warningMessage = '';
        $errorTitle = '';
        $errorMessage = '';

        if ($hasInvalidSignature) {
            $status = 'error';
            $errorTitle = 'Verifikasi Tanda Tangan Gagal';
            $errorMessage = 'Tanda tangan digital kriptografi pada berkas ini tidak valid atau telah mengalami perubahan.';
        } elseif ($hasRevokedCert || $hasExpiredCert || $hasNotYetValidCert) {
            $status = 'warning';
            $warningTitle = 'Dokumen Terdaftar dengan Peringatan';
            $certIssues = [];

            if ($hasRevokedCert) {
                $certIssues[] = 'telah dicabut';
            }

            if ($hasExpiredCert) {
                $certIssues[] = 'sudah kedaluwarsa';
            }

            if ($hasNotYetValidCert) {
                $certIssues[] = 'belum memasuki masa berlaku';
            }

            if (count($certIssues) === 1) {
                $certIssuePhrase = $certIssues[0];
            } elseif (count($certIssues) === 2) {
                $certIssuePhrase = $certIssues[0] . ' dan ' . $certIssues[1];
            } else {
                $certIssuePhrase = $certIssues[0] . ', ' . $certIssues[1] . ', dan ' . $certIssues[2];
            }

            if ($isDocExpired) {
                $warningMessage = 'Berkas cocok dengan arsip Tandain dan tanda tangan digital terverifikasi, namun dokumen sudah melewati masa berlaku serta sertifikat digital penandatangan ' . $certIssuePhrase . '.';
            } else {
                $warningMessage = 'Berkas cocok dengan arsip Tandain dan tanda tangan digital terverifikasi, namun sertifikat digital penandatangan ' . $certIssuePhrase . '.';
            }
        } elseif ($isDocExpired) {
            $status = 'expired';
        }

        return [
            'document' => $document,
            'status' => $status,
            'is_expired' => $isDocExpired,
            'warning_title' => $warningTitle,
            'warning_message' => $warningMessage,
            'error_title' => $errorTitle,
            'error_message' => $errorMessage,
        ];
    }
}
