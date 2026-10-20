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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $captcha  = trim($_POST['captcha'] ?? '');

            if (empty($email) || empty($password) || empty($captcha)) {
                $data['error'] = 'Data tidak boleh kosong.';

            } elseif ($captcha !== ($_SESSION['captcha_code'] ?? '')) {
                $data['error'] = 'Kode captcha salah.';

            } else {
                $userModel = $this->model('User');

                $user = $userModel->findByEmail($email);

                if ($user && password_verify($password, $user['password_hash'])) {
                    unset($_SESSION['captcha_code']);

                    $_SESSION['user'] = [
                        'id'        => $user['id'],
                        'full_name' => $user['full_name'],
                        'email'     => $user['email'],
                        'role_id'   => $user['role_id'],
                    ];

                    $this->redirect('/Tandain/dashboard');

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
            $this->redirect('/Tandain/dashboard');
        }

        $data['title'] = 'Register - Tandain';
        $data['error'] = '';
        $data['validate'] = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $identityNumber = trim($_POST['identity_number'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $repassword = trim($_POST['repassword'] ?? '');
            $captcha = trim($_POST['captcha'] ?? '');

            if (empty($fullName) || empty($identityNumber) || empty($email) || empty($password) || empty($repassword) || empty($captcha)) {
                $data['error'] = 'Data tidak boleh kosong.';

            } elseif ($captcha !== ($_SESSION['captcha_code'] ?? '')) {
                $data['error'] = 'Kode captcha salah.';

            } elseif ($password !== $repassword) {
                $data['validate'] = 'Password tidak sama.';

            } else {
                $userModel = $this->model('User');

                $existing = $userModel->findByEmail($email);

                if ($existing) {
                    $data['error'] = 'Email sudah terdaftar.';

                } else {
                    $role = $userModel->findRoleByName('mahasiswa');

                    $result = $userModel->create([
                        'full_name'       => $fullName,
                        'identity_number' => $identityNumber,
                        'email'           => $email,
                        'password_hash'   => password_hash($password, PASSWORD_DEFAULT),
                        'role_id'         => $role['id'],
                    ]);

                    if ($result) {
                        unset($_SESSION['captcha_code']);

                        $this->redirect('/Tandain/login');

                    } else {
                        $data['error'] = 'Registrasi gagal, coba lagi.';
                    }
                }
            }
        }

        $this->view('auth/register', $data);
    }

    public function logout()
    {
        unset($_SESSION['user']);

        session_destroy();

        $this->redirect('/Tandain/login');
    }
}
