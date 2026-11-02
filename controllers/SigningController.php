<?php

class SigningController extends Controller
{
    public function index()
    {
        $this->authCheck();

        $data['title'] = 'Dokumen Ditandatangani - Tandain';
        $data['active_menu'] = 'signed';
        $data['user'] = $_SESSION['user'];

        $this->view('document/signed', $data);
    }
}

