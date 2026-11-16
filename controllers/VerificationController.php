<?php

class VerificationController extends Controller
{
    public function index($documentId = null)
    {
        $status = $_GET['status'] ?? 'upload';

        $data['title'] = 'Verifikasi Dokumen - Tandain';
        $data['status'] = $status;
        $data['document'] = null;
        $data['file_name'] = '';
        $data['file_size'] = 0;
        $data['file_hash'] = '';
        $data['is_expired'] = false;

        if ($documentId !== null) {
            $documentId = trim((string) $documentId);

            if ($documentId === '' || !preg_match('/\A[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}\z/i', $documentId)) {
                $data['status'] = 'error';
                $data['file_name'] = 'ID QR Dokumen Tidak Valid';
                $data['file_hash'] = $documentId;
            } else {
                $documentModel = $this->model('Document');
                $document = $documentModel->verifyDocument($documentId);

                if ($document) {
                    $isExpired = false;

                    if (!empty($document['expires_at'])) {
                        $isExpired = (strtotime($document['expires_at']) < time());
                    }

                    $data['status'] = $isExpired ? 'expired' : 'success';
                    $data['document'] = $document;
                    $data['is_expired'] = $isExpired;
                    $data['file_hash'] = $document['final_sha256_hash'] ?: $document['initial_sha256_hash'];
                    $data['file_name'] = basename($document['final_file_path'] ?: $document['original_file_path']);
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
        $data['status'] = 'upload';
        $data['document'] = null;
        $data['file_name'] = '';
        $data['file_size'] = 0;
        $data['file_hash'] = '';
        $data['is_expired'] = false;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['document_file']['tmp_name'])) {
            $data['status'] = 'error';
            $data['file_name'] = 'Tidak ada berkas yang diunggah';
            $data['file_hash'] = '-';
        } elseif (($_FILES['document_file']['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || !is_uploaded_file($_FILES['document_file']['tmp_name'])) {
            $data['status'] = 'error';
            $data['file_name'] = $_FILES['document_file']['name'] ?? 'Berkas tidak valid';
            $data['file_hash'] = '-';
        } else {
            $originalName = trim((string) ($_FILES['document_file']['name'] ?? 'dokumen.pdf'));
            $fileSize = (int) ($_FILES['document_file']['size'] ?? 0);
            $tmpPath = $_FILES['document_file']['tmp_name'];
            $uploadedHash = hash_file('sha256', $tmpPath);

            $data['file_name'] = $originalName;
            $data['file_size'] = $fileSize;
            $data['file_hash'] = $uploadedHash ?: '-';

            if (!$uploadedHash) {
                $data['status'] = 'error';
            } else {
                $documentModel = $this->model('Document');
                $document = $documentModel->verifyDocument($uploadedHash);

                if ($document) {
                    $isExpired = false;

                    if (!empty($document['expires_at'])) {
                        $isExpired = (strtotime($document['expires_at']) < time());
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
}
