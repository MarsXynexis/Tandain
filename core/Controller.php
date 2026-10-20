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
}
