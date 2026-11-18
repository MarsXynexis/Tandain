<?php

use League\OAuth2\Client\Provider\Google;

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
            $data['username'] = $username;

            if (empty($username) || empty($password)) {
                $data['error'] = 'Data tidak boleh kosong.';
            } else {
                $userModel = $this->model('User');

                $user = $userModel->findByUsername($username);

                if ($user && password_verify($password, $user['password_hash'])) {
                    if ($user['status'] !== 'ACTIVE') {
                        $data['error'] = 'Akun Anda ditangguhkan. Hubungi Administrator Tata Usaha.';
                    } else {
                        $this->establishUserSession($user);

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

    public function google()
    {
        $redirectUrl = '/Tandain/login';
        $provider = $this->getGoogleProvider();

        if (isset($_SESSION['user'])) {
            $redirectUrl = '/Tandain/dashboard';
        } elseif (!$provider) {
            $this->setFlash('error', 'Konfigurasi Google OAuth belum diatur pada file .env.');
        } else {
            $redirectUrl = $provider->getAuthorizationUrl(['prompt' => 'select_account']);

            $_SESSION['google_oauth_state'] = $provider->getState();
        }

        $this->redirect($redirectUrl);
    }

    public function googleCallback()
    {
        $redirectUrl = '/Tandain/login';
        $provider = $this->getGoogleProvider();
        $sessionState = $_SESSION['google_oauth_state'] ?? '';
        $requestState = $_GET['state'] ?? '';
        $code = $_GET['code'] ?? '';

        unset($_SESSION['google_oauth_state']);

        if (isset($_SESSION['user'])) {
            $redirectUrl = '/Tandain/dashboard';
        } elseif (!$provider || empty($code) || empty($sessionState) || $requestState !== $sessionState) {
            $this->setFlash('error', 'Login Google gagal atau dibatalkan.');
        } else {
            try {
                $token = $provider->getAccessToken('authorization_code', ['code' => $code]);
                $googleUser = $provider->getResourceOwner($token);
                $email = strtolower(trim((string) $googleUser->getEmail()));

                $userModel = $this->model('User');
                $user = $userModel->findByEmail($email);

                if (!$user) {
                    $this->setFlash('error', 'Email ' . $email . ' tidak terdaftar di sistem Tandain.');
                } elseif ($user['status'] !== 'ACTIVE') {
                    $this->setFlash('error', 'Akun Anda ditangguhkan. Hubungi Administrator Tata Usaha.');
                } else {
                    $this->establishUserSession($user);

                    $redirectUrl = '/Tandain/dashboard';
                }
            } catch (Exception $e) {
                $this->setFlash('error', 'Gagal memverifikasi akun Google.');
            }
        }

        $this->redirect($redirectUrl);
    }

    public function logout()
    {
        unset($_SESSION['user']);

        session_destroy();

        $this->redirect('/Tandain/login');
    }

    private function establishUserSession($user)
    {
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
    }

    private function getGoogleProvider()
    {
        $env = is_file('.env') ? (parse_ini_file('.env') ?: []) : [];
        $baseUrl = rtrim(trim($env['APP_URL'] ?? ''), '/');
        $clientId = trim($env['GOOGLE_CLIENT_ID'] ?? '');
        $clientSecret = trim($env['GOOGLE_CLIENT_SECRET'] ?? '');
        $redirectUri = trim($env['GOOGLE_REDIRECT_URI'] ?? '');
        $provider = null;

        if ($redirectUri === '' && $baseUrl !== '') {
            $redirectUri = $baseUrl . '/auth/googleCallback';
        }

        if ($clientId !== '' && $clientSecret !== '' && $redirectUri !== '') {
            $provider = new Google([
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => $redirectUri,
            ]);
        }

        return $provider;
    }
}
