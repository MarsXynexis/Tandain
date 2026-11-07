<?php

class InboxController extends Controller
{
    public function index()
    {
        $this->authCheck();

        $data['title'] = 'Persetujuan Dokumen - Tandain';
        $data['active_menu'] = 'signed';
        $data['user'] = $_SESSION['user'];

        $this->view('inbox/index', $data);
    }

}
