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

    protected function refreshAuthenticatedUser()
    {
        $isAuthenticated = false;

        if (isset($_SESSION['user']['id'])) {
            $userModel = $this->model('User');
            $freshUser = $userModel->findById($_SESSION['user']['id']);

            if ($freshUser && ($freshUser['status'] ?? '') === 'ACTIVE') {
                $_SESSION['user'] = [
                    'id' => $freshUser['id'],
                    'full_name' => $freshUser['full_name'],
                    'email' => $freshUser['email'],
                    'role_id' => $freshUser['role_id'],
                    'role_name' => $freshUser['role_name'],
                    'position' => $freshUser['position'],
                    'study_program' => $freshUser['study_program'] ?? null,
                    'role_display_name' => $freshUser['role_display_name'],
                ];

                $isAuthenticated = true;
            } else {
                unset($_SESSION['user'], $_SESSION['csrf_token']);

                session_regenerate_id(true);
            }
        }

        return $isAuthenticated;
    }

    public function authCheck()
    {
        $hadSession = isset($_SESSION['user']);

        if (!$this->refreshAuthenticatedUser()) {
            if ($hadSession) {
                $this->setFlash('error', 'Akun Anda tidak aktif atau telah ditangguhkan. Hubungi Administrator Tata Usaha.');
            }

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

    protected function getCsrfToken()
    {
        return csrfToken();
    }

    protected function verifyCsrfToken($token)
    {
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        $isValid = is_string($token)
            && is_string($sessionToken)
            && $sessionToken !== ''
            && hash_equals($sessionToken, $token);

        return $isValid;
    }
}
