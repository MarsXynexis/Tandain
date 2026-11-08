<?php

class DocumentController extends Controller
{
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
}
