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
        $data['error'] = '';
        $data['form'] = [
            'full_name' => '',
            'identity_number' => '',
            'email' => '',
            'position' => '',
            'role_id' => '',
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullName = trim($_POST['full_name'] ?? '');
            $identityNumber = trim($_POST['identity_number'] ?? '');
            $email = strtolower(trim($_POST['email'] ?? ''));
            $position = trim($_POST['position'] ?? '');
            $roleId = trim($_POST['role_id'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirmation = $_POST['password_confirmation'] ?? '';

            $data['form'] = [
                'full_name' => $fullName,
                'identity_number' => $identityNumber,
                'email' => $email,
                'position' => $position,
                'role_id' => $roleId,
            ];

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
                    'position' => $position,
                    'role_id' => $roleId,
                    'password_hash' => password_hash($password, PASSWORD_BCRYPT),
                ]);

                $this->setFlash('success', 'Akun ' . $fullName . ' berhasil ditambahkan.');

                $this->redirect('/Tandain/accounts');
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
            $data['error'] = '';
            $data['form'] = [
                'full_name' => $targetUser['full_name'],
                'identity_number' => $targetUser['identity_number'],
                'email' => $targetUser['email'],
                'position' => $targetUser['position'] ?? '',
                'role_id' => $targetUser['role_id'],
                'status' => $targetUser['status'],
            ];

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $fullName = trim($_POST['full_name'] ?? '');
                $identityNumber = trim($_POST['identity_number'] ?? '');
                $email = strtolower(trim($_POST['email'] ?? ''));
                $position = trim($_POST['position'] ?? '');
                $roleId = trim($_POST['role_id'] ?? '');
                $status = trim($_POST['status'] ?? '');

                $data['form'] = [
                    'full_name' => $fullName,
                    'identity_number' => $identityNumber,
                    'email' => $email,
                    'position' => $position,
                    'role_id' => $roleId,
                    'status' => $status,
                ];

                $error = $this->validateAccount($userModel, $data['roles'], $data['form'], $id);
                $isSelf = ($targetUser['id'] === $_SESSION['user']['id']);

                if ($error !== '') {
                    $data['error'] = $error;
                } elseif (!in_array($status, ['ACTIVE', 'SUSPENDED'], true)) {
                    $data['error'] = 'Status akun yang dipilih tidak valid.';
                } elseif ($isSelf && ($roleId !== $targetUser['role_id'] || $status !== $targetUser['status'])) {
                    $data['error'] = 'Peran dan status akun Anda sendiri tidak dapat diubah dari halaman ini.';
                } else {
                    $userModel->updateUser($id, [
                        'full_name' => $fullName,
                        'identity_number' => $identityNumber,
                        'email' => $email,
                        'position' => $position,
                        'role_id' => $roleId,
                        'status' => $status,
                    ]);

                    if ($isSelf) {
                        $_SESSION['user']['full_name'] = $fullName;
                        $_SESSION['user']['email'] = $email;
                        $_SESSION['user']['position'] = $position;
                    }

                    $this->setFlash('success', 'Akun ' . $fullName . ' berhasil diperbarui.');

                    $this->redirect('/Tandain/accounts');
                }
            }

            $this->view('accounts/edit', $data);
        }
    }

    public function toggle($id = null)
    {
        $this->adminCheck();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($id)) {
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
            if (empty($id)) {
                $this->setFlash('error', 'Akun yang ingin diubah tidak ditemukan.');
            } else {
                $redirectUrl = '/Tandain/accounts/edit/' . $id;

                $userModel = $this->model('User');
                $targetUser = $userModel->findById($id);

                $newPassword = $_POST['new_password'] ?? '';
                $confirmation = $_POST['new_password_confirmation'] ?? '';

                if (empty($targetUser)) {
                    $this->setFlash('error', 'Akun yang ingin diubah tidak ditemukan.');
                } elseif (strlen($newPassword) < 8) {
                    $this->setFlash('error', 'Kata sandi baru minimal terdiri dari 8 karakter.');
                } elseif ($newPassword !== $confirmation) {
                    $this->setFlash('error', 'Konfirmasi kata sandi baru tidak sama.');
                } else {
                    $userModel->resetPassword($id, password_hash($newPassword, PASSWORD_BCRYPT));

                    $this->setFlash('success', 'Kata sandi akun ' . $targetUser['full_name'] . ' berhasil diperbarui.');
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
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $error = 'Format alamat email tidak valid.';
        } elseif (!$this->isCampusEmail($data['email'])) {
            $error = 'Gunakan alamat email resmi kampus (@pnj.ac.id atau @stu.pnj.ac.id).';
        } elseif (!in_array($data['role_id'], $validRoleIds, true)) {
            $error = 'Peran akun yang dipilih tidak valid.';
        } else {
            $existingEmail = $userModel->findByEmail($data['email']);
            $existingIdentity = $userModel->findByIdentityNumber($data['identity_number']);

            if (!empty($existingEmail) && $existingEmail['id'] !== $excludeId) {
                $error = 'Alamat email kampus sudah terdaftar.';
            } elseif (!empty($existingIdentity) && $existingIdentity['id'] !== $excludeId) {
                $error = 'Nomor induk sudah terdaftar.';
            }
        }

        return $error;
    }

    private function isCampusEmail($email)
    {
        return (bool) preg_match('/^[^@\s]+@(pnj\.ac\.id|stu\.pnj\.ac\.id)$/i', $email);
    }
}
