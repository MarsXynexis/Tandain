<?php

class VerificationController extends Controller
{
    public function index()
    {
        $status = $_GET['status'] ?? 'upload';

        $data['title'] = 'Verifikasi Dokumen - Tandain';
        $data['status'] = $status;

        $this->view('verification/index', $data);
    }
}
