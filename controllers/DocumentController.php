<?php

class DocumentController extends Controller
{
    public function index()
    {
        $this->authCheck();

        $data['title'] = 'Dokumen Diunggah - Tandain';
        $data['active_menu'] = 'uploaded';
        $data['user'] = $_SESSION['user'];

        $this->view('document/uploaded', $data);
    }

    public function create()
    {
        $this->authCheck();

        $data['title'] = 'Ajukan Dokumen - Tandain';
        $data['active_menu'] = 'apply';
        $data['user'] = $_SESSION['user'];

        $this->view('document/create', $data);
    }

    public function uploaded()
    {
        $this->authCheck();

        $data['title'] = 'Dokumen Diunggah - Tandain';
        $data['active_menu'] = 'uploaded';
        $data['user'] = $_SESSION['user'];

        $this->view('document/uploaded', $data);
    }

    public function signed()
    {
        $this->authCheck();

        $data['title'] = 'Dokumen Ditandatangani - Tandain';
        $data['active_menu'] = 'signed';
        $data['user'] = $_SESSION['user'];

        $this->view('document/signed', $data);
    }
}

