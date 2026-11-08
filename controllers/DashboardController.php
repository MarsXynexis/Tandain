<?php

class DashboardController extends Controller
{
    public function index()
    {
        $this->authCheck();

        $docModel = $this->model('Document');

        $userRole = $_SESSION['user']['role_name'] ?? '';

        if (empty($userRole) && ($_SESSION['user']['role_display_name'] ?? '') === 'Administrator Tata Usaha') {
            $userRole = 'admin_tu';
        }

        $isAdmin = ($userRole === 'admin_tu');

        if ($isAdmin) {
            $data['doc_stats'] = $docModel->getDashboardStatistics();
            $data['recent_documents'] = $docModel->getAllDocumentsWithDetails('', '', 5);
        } else {
            $data['recent_documents'] = $docModel->getAllDocumentsWithDetails('', '', 5, $_SESSION['user']['id']);
        }

        $data['title'] = 'Dashboard - Tandain';
        $data['active_menu'] = 'dashboard';
        $data['user'] = $_SESSION['user'];
        $data['is_admin'] = $isAdmin;

        $this->view('dashboard/index', $data);
    }
}
