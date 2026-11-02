<?php

class DashboardController extends Controller
{
    public function index()
    {
        $this->authCheck();

        $data['title'] = 'Dashboard - Tandain';
        $data['active_menu'] = 'dashboard';
        $data['user'] = $_SESSION['user'];

        $this->view('dashboard/index', $data);
    }
}
