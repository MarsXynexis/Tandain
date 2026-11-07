<?php

class AuthController extends Controller
{
    public function index()
    {
        if (isset($_SESSION['user'])) {
            $this->redirect('/Tandain/dashboard');
        }

        $data['title'] = 'Login - Tandain';
        $data['error'] = '';
        $data['username'] = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? $_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $captcha = trim($_POST['captcha'] ?? '');

            $data['username'] = $username;

            if (empty($username) || empty($password) || empty($captcha)) {
                $data['error'] = 'Data tidak boleh kosong.';

            } elseif ($captcha !== ($_SESSION['captcha_code'] ?? '')) {
                $data['error'] = 'Kode captcha salah.';

            } else {
                $userModel = $this->model('User');

                $user = $userModel->findByUsername($username);

                if ($user && password_verify($password, $user['password_hash'])) {
                    if ($user['status'] !== 'ACTIVE') {
                        $data['error'] = 'Akun Anda ditangguhkan. Hubungi Administrator Tata Usaha.';
                    } else {
                        unset($_SESSION['captcha_code']);

                        session_regenerate_id(true);

                        $_SESSION['user'] = [
                            'id' => $user['id'],
                            'full_name' => $user['full_name'],
                            'email' => $user['email'],
                            'role_id' => $user['role_id'],
                            'role_name' => $user['role_name'],
                            'position' => $user['position'],
                            'role_display_name' => $user['role_display_name'],
                        ];

                        $this->redirect('/Tandain/dashboard');
                    }
                } else {
                    $data['error'] = 'Email atau password salah.';
                }
            }
        }

        $this->view('auth/login', $data);
    }

    public function register()
    {
        if (isset($_SESSION['user'])) {
            $redirectUrl = '/Tandain/dashboard';
        } else {
            $this->setFlash('info', 'Pendaftaran akun hanya dapat dilakukan oleh Administrator Tata Usaha.');

            $redirectUrl = '/Tandain/login';
        }

        $this->redirect($redirectUrl);
    }

    public function logout()
    {
        unset($_SESSION['user']);

        session_destroy();

        $this->redirect('/Tandain/login');
    }
}
