<?php

class VerificationController extends Controller
{
    public function index($documentId = null)
    {
        $status = $_GET['status'] ?? 'upload';

        $data['title'] = 'Verifikasi Dokumen - Tandain';
        $data['status'] = $status;

    
        if ($documentId !== null) {
            if (!is_string($documentId) || !preg_match('/\A[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}\z/i', $documentId)) {
                $data['status'] = 'not_found';
                $this->view('verification/index', $data);
                return;
            }

            $documentModel = $this->model('Document');
            $document = $documentModel->verifyDocument($documentId);
            if (!$document) {
                $data['status'] = 'not_found';
            } else {
                $data['status'] = 'document';
                $data['document'] = $document;
            }
        }

        $this->view('verification/index', $data);
    }

    public function check()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || 
            empty($_FILES['document_file']['tmp_name'])) {
            $this->redirect('/Tandain/verification?status=error');
            return;
        }

        $tmpPath = $_FILES['document_file']['tmp_name'];
        $uploadedHash = hash_file('sha256', $tmpPath);

        $documentModel = $this->model('Document');
        $doc           = $documentModel->findByHash($uploadedHash);

        if ($doc && (
            hash_equals($doc['final_sha256_hash'] ?? '', $uploadedHash) ||
            hash_equals($doc['initial_sha256_hash'] ?? '', $uploadedHash)
        )) {
            $data = [
                'title'     => 'Verifikasi Dokumen - Tandain',
                'status'    => 'success',
                'document'  => $doc,
            ];
        } else {
            $data = [
                'title'     => 'Verifikasi Dokumen - Tandain',
                'status'    => 'error',
            ];
        }

        $this->view('verification/index', $data);
    }
}
?>