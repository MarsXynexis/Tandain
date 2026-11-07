<?php

class Controller
{
    public function view($view, $data = [])
    {
        extract($data);

        require_once 'views/' . $view . '.php';
    }

    public function model($model)
    {
        require_once 'models/' . $model . '.php';

        return new $model;
    }

    public function redirect($url)
    {
        header('Location: ' . $url);

        exit;
    }

    public function authCheck()
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/Tandain/login');
        }
    }

    public function adminCheck()
    {
        $this->authCheck();

        $role = $_SESSION['user']['role_name'] ?? '';
        $roleDisplay = $_SESSION['user']['role_display_name'] ?? '';

        if ($role !== 'admin_tu' && $roleDisplay !== 'Administrator Tata Usaha') {
            $this->redirect('/Tandain/dashboard');
        }
    }

    protected function setFlash($type, $message)
    {
        $_SESSION['flash'] = [
            'type' => $type,
            'message' => $message,
        ];
    }
}
