<?php

class AccountsController extends Controller
{
    public function index()
    {
        $this->adminCheck();

        $userModel = $this->model('User');

        $search = trim($_GET['search'] ?? '');
        $role = trim($_GET['role'] ?? '');
        $status = trim($_GET['status'] ?? '');

        $data['title'] = 'Manajemen Akun Civitas - Tandain';
        $data['active_menu'] = 'accounts';
        $data['user'] = $_SESSION['user'];
        $data['search'] = $search;
        $data['selected_role'] = $role;
        $data['selected_status'] = $status;
        $data['users'] = $userModel->getFilteredUsers($search, $role, $status);
        $data['roles'] = $userModel->getAllRoles();
        $data['stats'] = $userModel->getUserStatistics();
        $data['csrf_token'] = $this->getCsrfToken();

        $this->view('accounts/index', $data);
    }

    public function create()
    {
        $this->adminCheck();

        $userModel = $this->model('User');

        $data['title'] = 'Tambah Akun Civitas - Tandain';
        $data['active_menu'] = 'accounts_create';
        $data['user'] = $_SESSION['user'];
        $data['roles'] = $userModel->getAllRoles();
        $data['csrf_token'] = $this->getCsrfToken();
        $data['error'] = '';
        $data['form'] = [
            'full_name' => '',
            'identity_number' => '',
            'email' => '',
            'position' => '',
            'study_program' => '',
            'role_id' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $identityNumber = trim($_POST['identity_number'] ?? '');
            $email = strtolower(trim($_POST['email'] ?? ''));
            $position = trim($_POST['position'] ?? '');
            $studyProgram = trim($_POST['study_program'] ?? '');
            $roleId = trim($_POST['role_id'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirmation = $_POST['password_confirmation'] ?? '';
            $csrfToken = $_POST['csrf_token'] ?? '';

            $data['form'] = [
                'full_name' => $fullName,
                'identity_number' => $identityNumber,
                'email' => $email,
                'position' => $position,
                'study_program' => $studyProgram,
                'role_id' => $roleId,
            ];

            if (!$this->verifyCsrfToken($csrfToken)) {
                $data['error'] = 'Sesi formulir tidak valid. Silakan muat ulang halaman dan coba lagi.';
            } else {
                $error = $this->validateAccount($userModel, $data['roles'], $data['form']);

                if ($error !== '') {
                    $data['error'] = $error;
                } elseif (strlen($password) < 8) {
                    $data['error'] = 'Kata sandi awal minimal terdiri dari 8 karakter.';
                } elseif ($password !== $passwordConfirmation) {
                    $data['error'] = 'Konfirmasi kata sandi tidak sama.';
                } else {
                    $userModel->create([
                        'full_name' => $fullName,
                        'identity_number' => $identityNumber,
                        'email' => $email,
                        'position' => $position !== '' ? $position : null,
                        'study_program' => $studyProgram !== '' ? $studyProgram : null,
                        'role_id' => $roleId,
                        'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                    ]);

                    $this->setFlash('success', 'Akun ' . $fullName . ' berhasil ditambahkan.');

                    $this->redirect('/Tandain/accounts');
                }
            }
        }

        $this->view('accounts/create', $data);
    }

    public function edit($id = null)
    {
        $this->adminCheck();

        $userModel = $this->model('User');
        $targetUser = !empty($id) ? $userModel->findById($id) : null;

        if (empty($targetUser)) {
            $this->setFlash('error', 'Akun yang ingin diubah tidak ditemukan.');

            $this->redirect('/Tandain/accounts');
        } else {
            $data['title'] = 'Ubah Akun Civitas - Tandain';
            $data['active_menu'] = 'accounts_edit';
            $data['user'] = $_SESSION['user'];
            $data['target_user'] = $targetUser;
            $data['roles'] = $userModel->getAllRoles();
            $data['csrf_token'] = $this->getCsrfToken();
            $data['error'] = '';
            $data['form'] = [
                'full_name' => $targetUser['full_name'],
                'identity_number' => $targetUser['identity_number'],
                'email' => $targetUser['email'],
                'position' => $targetUser['position'] ?? '',
                'study_program' => $targetUser['study_program'] ?? '',
                'role_id' => $targetUser['role_id'],
                'status' => $targetUser['status'],
            ];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fullName = trim($_POST['full_name'] ?? '');
                $identityNumber = trim($_POST['identity_number'] ?? '');
                $email = strtolower(trim($_POST['email'] ?? ''));
                $position = trim($_POST['position'] ?? '');
                $studyProgram = trim($_POST['study_program'] ?? '');
                $roleId = trim($_POST['role_id'] ?? '');
                $status = trim($_POST['status'] ?? '');
                $csrfToken = $_POST['csrf_token'] ?? '';

                $data['form'] = [
                    'full_name' => $fullName,
                    'identity_number' => $identityNumber,
                    'email' => $email,
                    'position' => $position,
                    'study_program' => $studyProgram,
                    'role_id' => $roleId,
                    'status' => $status,
                ];

                if (!$this->verifyCsrfToken($csrfToken)) {
                    $data['error'] = 'Sesi formulir tidak valid. Silakan muat ulang halaman dan coba lagi.';
                } else {
                    $error = $this->validateAccount($userModel, $data['roles'], $data['form'], $id);
                    $isSelf = ($targetUser['id'] === $_SESSION['user']['id']);

                    if ($error !== '') {
                        $data['error'] = $error;
                    } elseif (!in_array($status, ['ACTIVE', 'SUSPENDED'], true)) {
                        $data['error'] = 'Status akun yang dipilih tidak valid.';
                    } elseif ($isSelf && ($roleId !== $targetUser['role_id'] || $status !== $targetUser['status'])) {
                        $data['error'] = 'Peran dan status akun Anda sendiri tidak dapat diubah dari halaman ini.';
                    } else {
                        $normalizedPosition = $position !== '' ? $position : null;
                        $normalizedStudyProgram = $studyProgram !== '' ? $studyProgram : null;

                        $userModel->updateUser($id, [
                            'full_name' => $fullName,
                            'identity_number' => $identityNumber,
                            'email' => $email,
                            'position' => $normalizedPosition,
                            'study_program' => $normalizedStudyProgram,
                            'role_id' => $roleId,
                            'status' => $status,
                        ]);

                        if ($isSelf) {
                            $_SESSION['user']['full_name'] = $fullName;
                            $_SESSION['user']['email'] = $email;
                            $_SESSION['user']['position'] = $normalizedPosition;
                            $_SESSION['user']['study_program'] = $normalizedStudyProgram;
                        }

                        $this->setFlash('success', 'Akun ' . $fullName . ' berhasil diperbarui.');

                        $this->redirect('/Tandain/accounts');
                    }
                }
            }

            $this->view('accounts/edit', $data);
        }
    }

    public function toggle($id = null)
    {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';

            if (!$this->verifyCsrfToken($csrfToken)) {
                $this->setFlash('error', 'Sesi formulir tidak valid. Silakan coba kembali.');
            } elseif (empty($id)) {
                $this->setFlash('error', 'Akun yang ingin diubah tidak ditemukan.');
            } elseif ($id === $_SESSION['user']['id']) {
                $this->setFlash('error', 'Anda tidak dapat menangguhkan akun sendiri.');
            } else {
                $userModel = $this->model('User');
                $targetUser = $userModel->findById($id);

                if (empty($targetUser)) {
                    $this->setFlash('error', 'Akun yang ingin diubah tidak ditemukan.');
                } else {
                    $userModel->toggleStatus($id);

                    $statusLabel = ($targetUser['status'] === 'ACTIVE') ? 'ditangguhkan' : 'diaktifkan';

                    $this->setFlash('success', 'Akun ' . $targetUser['full_name'] . ' berhasil ' . $statusLabel . '.');
                }
            }
        }

        $this->redirect('/Tandain/accounts');
    }

    public function resetPassword($id = null)
    {
        $this->adminCheck();

        $redirectUrl = '/Tandain/accounts';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';

            if (!$this->verifyCsrfToken($csrfToken)) {
                $this->setFlash('error', 'Sesi formulir tidak valid. Silakan coba kembali.');

                if (!empty($id)) {
                    $redirectUrl = '/Tandain/accounts/edit/' . rawurlencode((string) $id);
                }
            } elseif (empty($id)) {
                $this->setFlash('error', 'Akun yang ingin diubah tidak ditemukan.');
            } else {
                $userModel = $this->model('User');
                $targetUser = $userModel->findById($id);

                $newPassword = $_POST['new_password'] ?? '';
                $confirmation = $_POST['new_password_confirmation'] ?? '';

                if (empty($targetUser)) {
                    $this->setFlash('error', 'Akun yang ingin diubah tidak ditemukan.');
                } else {
                    $redirectUrl = '/Tandain/accounts/edit/' . rawurlencode((string) $targetUser['id']);

                    if (strlen($newPassword) < 8) {
                        $this->setFlash('error', 'Kata sandi baru minimal terdiri dari 8 karakter.');
                    } elseif ($newPassword !== $confirmation) {
                        $this->setFlash('error', 'Konfirmasi kata sandi baru tidak sama.');
                    } else {
                        $userModel->resetPassword($id, password_hash($newPassword, PASSWORD_BCRYPT));

                        $this->setFlash('success', 'Kata sandi akun ' . $targetUser['full_name'] . ' berhasil diperbarui.');
                    }
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    private function validateAccount($userModel, $roles, $data, $excludeId = null)
    {
        $error = '';
        $validRoleIds = array_column($roles, 'id');

        if (empty($data['full_name']) || empty($data['identity_number']) || empty($data['email']) || empty($data['role_id'])) {
            $error = 'Nama, nomor induk, email, dan peran wajib diisi.';
        } elseif (mb_strlen($data['full_name']) > 150) {
            $error = 'Nama lengkap maksimal terdiri dari 150 karakter.';
        } elseif (mb_strlen($data['identity_number']) > 50) {
            $error = 'Nomor induk maksimal terdiri dari 50 karakter.';
        } elseif (mb_strlen($data['email']) > 150) {
            $error = 'Alamat email maksimal terdiri dari 150 karakter.';
        } elseif (mb_strlen($data['position'] ?? '') > 100) {
            $error = 'Jabatan struktural maksimal terdiri dari 100 karakter.';
        } elseif (mb_strlen($data['study_program'] ?? '') > 100) {
            $error = 'Program studi maksimal terdiri dari 100 karakter.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Format alamat email tidak valid.';
        } elseif (!in_array($data['role_id'], $validRoleIds, true)) {
            $error = 'Peran akun yang dipilih tidak valid.';
        } else {
            $existingEmail = $userModel->findByEmail($data['email']);
            $existingIdentity = $userModel->findByIdentityNumber($data['identity_number']);

            if (!empty($existingEmail) && $existingEmail['id'] !== $excludeId) {
                $error = 'Alamat email sudah terdaftar.';
            } elseif (!empty($existingIdentity) && $existingIdentity['id'] !== $excludeId) {
                $error = 'Nomor induk sudah terdaftar.';
            }
        }

        return $error;
    }
}
