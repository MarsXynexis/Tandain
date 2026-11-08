<?php

class AuditController extends Controller
{
    public function index()
    {
        $this->adminCheck();

        $auditModel = $this->model('DocumentAuditLog');

        $search = trim($_GET['search'] ?? '');
        $action = trim($_GET['action'] ?? '');

        $data['title'] = 'Log Audit Dokumen - Tandain';
        $data['active_menu'] = 'audit';
        $data['user'] = $_SESSION['user'];
        $data['search'] = $search;
        $data['selected_action'] = $action;
        $data['logs'] = $auditModel->getFilteredLogs($search, $action, 100);
        $data['actions'] = $auditModel->getDistinctActions();
        $data['stats'] = $auditModel->getAuditStatistics();

        $this->view('audit/index', $data);
    }
}
