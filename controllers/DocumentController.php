<?php

class DocumentController extends Controller
{
    // Daftar dokumen (baru: ada search, filter, pagination)
    public function index()
    {
        $this->authCheck();

        $documentModel = $this->model('DocumentList');
        $userId = $_SESSION['user']['id'];

        $search = trim($_GET['q'] ?? '');
        $filter = $_GET['status'] ?? 'all';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 8;
        $offset = ($page - 1) * $perPage;

        $total = $documentModel->countDocuments($userId, $search, $filter);
        $totalPages = max(1, (int) ceil($total / $perPage));

        $data['title'] = 'Dokumen Diunggah - Tandain';
        $data['active_menu'] = 'uploaded';
        $data['user'] = $_SESSION['user'];
        $data['documents'] = $documentModel->getDocuments($userId, $search, $filter, $perPage, $offset);
        $data['counts'] = $documentModel->getStatusCounts($userId);
        $data['search'] = $search;
        $data['filter'] = $filter;
        $data['page'] = $page;
        $data['perPage'] = $perPage;
        $data['total'] = $total;
        $data['totalPages'] = $totalPages;

        $this->view('document/list', $data);
    }

    // Punyamu, tidak diubah
    public function create()
    {
        $this->authCheck();

        $data['title'] = 'Ajukan Dokumen - Tandain';
        $data['active_menu'] = 'apply';
        $data['user'] = $_SESSION['user'];

        $this->view('document/create', $data);
    }

    public function signed()
    {
        $this->authCheck();

        $data['title'] = 'Pesetujuan Dokumen - Tandain';
        $data['active_menu'] = 'signed';
        $data['user'] = $_SESSION['user'];

        $this->view('document/signed', $data);
    }

    // Detail satu dokumen: /Tandain/document/detail?id=...
    public function detail()
    {
        $this->authCheck();

        $id = $_GET['id'] ?? '';

        $documentModel = $this->model('DocumentList');
        $document = $documentModel->getDocumentById($id, $_SESSION['user']['id']);

        if (!$document) {
            $this->redirect('/Tandain/document');
        }

        // Hitung progres tanda tangan
        $signers = $documentModel->getSigners($document['id']);

        $signedCount = 0;
        foreach ($signers as $signer) {
            if ($signer['status'] === 'SIGNED') {
                $signedCount++;
            }
        }

        $totalSigners = count($signers);
        $percent = $totalSigners > 0 ? (int) (($signedCount / $totalSigners) * 100) : 0;

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