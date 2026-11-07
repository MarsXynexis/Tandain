<?php

class CertificateController extends Controller
{
    public function index()
    {
        $this->authCheck();

        $data['title'] = 'Sertifikat Digital - Tandain';
        $data['active_menu'] = 'certificate';
        $data['user'] = $_SESSION['user'];

        $this->view('certificate/index', $data);
    }

    public function create()
    {
        $this->authCheck();

        $data['title'] = 'Buat Sertifikat Baru - Tandain';
        $data['active_menu'] = 'certificate';
        $data['user'] = $_SESSION['user'];

        $this->view('certificate/create', $data);
    }

    public function sign()
    {
        $this->authCheck();

        $data['title'] = 'Gambar Tanda Tangan - Tandain';
        $data['active_menu'] = 'signature_image';
        $data['user'] = $_SESSION['user'];

        $this->view('certificate/sign', $data);
    }
}
