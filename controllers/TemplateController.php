<?php

class TemplateController extends Controller
{
    public function index()
    {
        $this->adminCheck();

        $data['title'] = 'Template Surat - Tandain';
        $data['active_menu'] = 'templates';
        $data['user'] = $_SESSION['user'];

        $this->view('template/index', $data);
    }
}
