<?php

class ArchiveController extends Controller
{
    public function index()
    {
        $this->adminCheck();

        $docModel = $this->model('Document');

        $search = trim($_GET['search'] ?? '');
        $status = trim($_GET['status'] ?? 'COMPLETED');

        if (!in_array($status, ['COMPLETED', 'REJECTED', 'CANCELLED', 'all'], true)) {
            $status = 'COMPLETED';
        }

        $data['title'] = 'Arsip Dokumen - Tandain';
        $data['active_menu'] = 'archive';
        $data['user'] = $_SESSION['user'];
        $data['search'] = $search;
        $data['current_status'] = $status;
        $data['documents'] = $docModel->getArchivedDocuments($search, $status);
        $data['stats'] = $docModel->getArchiveStatistics();

        $this->view('archive/index', $data);
    }
}
