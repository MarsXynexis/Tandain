<?php

class CertificateController extends Controller
{
    public function index()
    {
        $this->authCheck();

        if (!isset($_SESSION['certificate_csrf_token']) || !is_string($_SESSION['certificate_csrf_token'])) {
            $_SESSION['certificate_csrf_token'] = bin2hex(random_bytes(32));
        }

        $certificateModel = $this->model('Certificate');
        $userId = $_SESSION['user']['id'];


        $data['certificates'] = $certificateModel->getCertificatesByUserId($userId);
        $data['title'] = 'Sertifikat Digital - Tandain';
        $data['active_menu'] = 'certificate';
        $data['user'] = $_SESSION['user'];
        $data['csrf_token'] = $_SESSION['certificate_csrf_token'];

        $this->view('certificate/index', $data);
    }

    public function create()
    {
        $this->authCheck();

        if (!isset($_SESSION['certificate_csrf_token']) || !is_string($_SESSION['certificate_csrf_token'])) {
            $_SESSION['certificate_csrf_token'] = bin2hex(random_bytes(32));
        }

        $data['title'] = 'Buat Sertifikat Baru - Tandain';
        $data['active_menu'] = 'certificate';
        $data['user'] = $_SESSION['user'];
        $data['csrf_token'] = $_SESSION['certificate_csrf_token'];

        $this->view('certificate/create', $data);
    }

    public function sign()
    {
        $this->authCheck();

        if (!isset($_SESSION['signature_csrf_token']) || !is_string($_SESSION['signature_csrf_token'])) {
            $_SESSION['signature_csrf_token'] = bin2hex(random_bytes(32));
        }

        $data['title'] = 'Gambar Tanda Tangan - Tandain';
        $data['active_menu'] = 'signature_image';
        $userModel = $this->model('User');
        $data['user'] = $userModel->findById($_SESSION['user']['id']);
        $data['csrf_token'] = $_SESSION['signature_csrf_token'];

        $this->view('certificate/sign', $data);
    }

    public function store()
    {
        $this->authCheck();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/Tandain/certificate/create');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['certificate_csrf_token'] ?? '';

        if (
            !is_string($csrfToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $csrfToken)
        ) {
            $this->setFlash('error', 'Permintaan tidak valid atau sesi telah kedaluwarsa. Silakan coba lagi.');
            $this->redirect('/Tandain/certificate/create');
        }

        unset($_SESSION['certificate_csrf_token']);

        $passphrase = $_POST['passphrase'] ?? '';
        $passphraseConfirmation = $_POST['passphrase_confirmation'] ?? '';
        $accountPassword = $_POST['account_password'] ?? '';

        if (
            !is_string($passphrase)
            || !is_string($passphraseConfirmation)
            || !is_string($accountPassword)
            || strlen($passphrase) < 4
            || !preg_match('/[a-z]/', $passphrase)
            || !preg_match('/[0-9]/', $passphrase)
        ) {
            $this->setFlash('error', 'Passphrase harus minimal 4 karakter dan mengandung huruf kecil serta angka.');
            $this->redirect('/Tandain/certificate/create');
        }

        if ($passphrase !== $passphraseConfirmation) {
            $this->setFlash('error', 'Passphrase dan konfirmasi tidak cocok.');
            $this->redirect('/Tandain/certificate/create');
        }

        $userModel = $this->model('User');
        $userId = $_SESSION['user']['id'];
        $user = $userModel->findById($userId);

        if (!$user || !password_verify($accountPassword, $user['password_hash'] ?? '')) {
            $this->setFlash('error', 'Password akun tidak valid.');
            $this->redirect('/Tandain/certificate/create');
        }

        if (password_verify($passphrase, $user['password_hash'])) {
            $this->setFlash('error', 'Passphrase harus berbeda dari password akun.');
            $this->redirect('/Tandain/certificate/create');
        }

        $certificateModel = $this->model('Certificate');

        try {
            $keypair = $certificateModel->generateRSA($passphrase);
        } catch (RuntimeException $exception) {
            error_log('Certificate RSA generation failed: ' . $exception->getMessage());
            $this->setFlash('error', 'Gagal membuat kunci sertifikat. Silakan coba lagi.');
            $this->redirect('/Tandain/certificate/create');
        }

        $serialNumber = 'CERT-PNJ-TIK-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $validFrom = date('Y-m-d H:i:s');
        $validUntil = date('Y-m-d H:i:s', strtotime('+1 year'));

        $result = $certificateModel->createCertificate(
            $userId,
            $keypair['private_key'],
            $keypair['public_key'],
            $serialNumber,
            $validFrom,
            $validUntil
        );

        if ($result) {
            $this->setFlash('success', 'Sertifikat digital berhasil dibuat.');
            $this->redirect('/Tandain/certificate');
        }

        $this->setFlash('error', 'Terjadi kesalahan saat membuat sertifikat.');
        $this->redirect('/Tandain/certificate/create');
    }

    public function revoke()
    {
        $this->authCheck();

        $certificateIdInput = $_POST['certificate_id'] ?? '';
        $certificateId = is_string($certificateIdInput) ? trim($certificateIdInput) : '';
        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['certificate_csrf_token'] ?? '';

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
            || !preg_match('/\A[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}\z/i', $certificateId)
            || !is_string($csrfToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $csrfToken)
        ) {
            $this->setFlash('error', 'Permintaan pencabutan sertifikat tidak valid. Silakan coba lagi.');
            $this->redirect('/Tandain/certificate');
        }

        $certificateModel = $this->model('Certificate');
        $userId = $_SESSION['user']['id'];
        try {
            $revoked = $certificateModel->revokeCertificate($userId, $certificateId);
        } catch (Throwable $exception) {
            error_log('Certificate revocation failed for certificate ' . $certificateId . ': ' . $exception->getMessage());
            $this->setFlash('error', 'Terjadi kesalahan saat mencabut sertifikat. Silakan coba lagi.');
            $this->redirect('/Tandain/certificate');
        }

        if ($revoked === true) {
            $this->setFlash('success', 'Sertifikat digital berhasil dicabut.');
        } elseif ($revoked === false) {
            $this->setFlash('error', 'Sertifikat tidak ditemukan, bukan milik Anda, atau sudah tidak aktif.');
        } else {
            $this->setFlash('error', 'Terjadi kesalahan saat mencabut sertifikat.');
        }

        $this->redirect('/Tandain/certificate');

    }

    public function updateSignature()
    {
        $this->authCheck();
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['message' => 'Metode permintaan tidak diizinkan.']);
            return;
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['signature_csrf_token'] ?? '';
        if (
            !is_string($csrfToken)
            || !is_string($sessionToken)
            || $sessionToken === ''
            || !hash_equals($sessionToken, $csrfToken)
        ) {
            http_response_code(403);
            echo json_encode(['message' => 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.']);
            return;
        }

        $file = $_FILES['signature_file'] ?? null;
        if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['message' => 'Pilih berkas PNG atau buat tanda tangan pada kanvas.']);
            return;
        }

        if ($file['size'] <= 0 || $file['size'] > 2 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['message' => 'Ukuran berkas harus lebih dari 0 dan maksimal 2MB.']);
            return;
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if ($mimeType !== 'image/png' || !$imageInfo || $imageInfo[2] !== IMAGETYPE_PNG) {
            http_response_code(400);
            echo json_encode(['message' => 'Berkas harus berupa gambar PNG yang valid.']);
            return;
        }

        $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'signatures';
        if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
            error_log('Unable to create signature upload directory: ' . $uploadDirectory);
            $_SESSION['flash'] = 'Gagal menyiapkan penyimpanan tanda tangan.';
            http_response_code(500);
            echo json_encode(['message' => 'Gagal menyiapkan penyimpanan tanda tangan.']);
            return;
        }

        $fileName = bin2hex(random_bytes(16)) . '.png';
        $destination = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            http_response_code(500);
            $_SESSION['flash'] = 'Gagal menyimpan berkas tanda tangan.';
            echo json_encode(['message' => 'Gagal menyimpan berkas tanda tangan.']);
            return;
        }

        $relativePath = 'uploads/signatures/' . $fileName;

        $userModel = $this->model('User');

        try {
            $user = $userModel->findById($_SESSION['user']['id']);
            if (!$user) {
                throw new RuntimeException('Authenticated user record was not found.');
            }

            $updated = $userModel->updateSignatureImagePath($_SESSION['user']['id'], $relativePath);
        } catch (Throwable $exception) {
            @unlink($destination);
            error_log('Failed to update signature image path: ' . $exception->getMessage());
            http_response_code(500);
            $_SESSION['flash'] = 'Gagal menyimpan tanda tangan ke akun.';
            echo json_encode(['message' => 'Gagal menyimpan tanda tangan ke akun.']);
            return;
        }

        if (!$updated) {
            @unlink($destination);
            http_response_code(500);
            $_SESSION['flash'] = 'Gagal menyimpan tanda tangan ke akun.';
            echo json_encode(['message' => 'Gagal menyimpan tanda tangan ke akun.']);
            return;
        }

        $oldPath = $user['signature_image_path'] ?? '';
        $oldPrefix = 'uploads/signatures/';
        if (is_string($oldPath) && str_starts_with($oldPath, $oldPrefix)) {
            $oldFile = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $oldPath);
            if (is_file($oldFile) && realpath(dirname($oldFile)) === realpath($uploadDirectory)) {
                unlink($oldFile);
            }
        }

        echo json_encode([
            'message' => 'Tanda tangan berhasil disimpan.',
            'image_url' => '/Tandain/' . $relativePath . '?v=' . filemtime($destination),
        ]);
    }

    public function qr($documentId = null)
    {
        $this->authCheck();

        if (!is_string($documentId) || !preg_match('/\A[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}\z/i', $documentId)) {
            http_response_code(404);
            exit;
        }

        $documentSignerModel = $this->model('DocumentSigner');
        $document = $documentSignerModel->getDetailDocumentById($documentId, $_SESSION['user']['id']);
        if (!$document) {
            http_response_code(404);
            exit;
        }

        require_once dirname(__DIR__) . '/vendor/tecnickcom/tcpdf/tcpdf_barcodes_2d.php';

        $environment = parse_ini_file(dirname(__DIR__) . '/.env') ?: [];
        $appUrl = trim($environment['APP_URL'] ?? '');
        if ($appUrl !== '') {
            $appUrlParts = parse_url($appUrl);
            if (
                !is_array($appUrlParts)
                || !in_array($appUrlParts['scheme'] ?? '', ['http', 'https'], true)
                || empty($appUrlParts['host'])
                || isset($appUrlParts['user'])
                || isset($appUrlParts['pass'])
                || isset($appUrlParts['query'])
                || isset($appUrlParts['fragment'])
            ) {
                http_response_code(500);
                exit('APP_URL tidak valid.');
            }

            $appBaseUrl = rtrim($appUrl, '/');
        } else {
            $host = $_SERVER['SERVER_NAME'] ?? '';
            if (!preg_match('/\A(?:[a-z0-9.-]+|\[[a-f0-9:]+\])(?::[0-9]{1,5})?\z/i', $host)) {
                http_response_code(500);
                exit('Konfigurasi host aplikasi tidak valid. Tetapkan APP_URL.');
            }

            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $port = (int) ($_SERVER['SERVER_PORT'] ?? 0);
            $defaultPort = ($scheme === 'https') ? 443 : 80;
            $hostWithPort = ($port > 0 && $port !== $defaultPort) ? $host . ':' . $port : $host;
            $appBaseUrl = $scheme . '://' . $hostWithPort . '/Tandain';
        }

        $verificationUrl = $appBaseUrl . '/verify/' . rawurlencode($document['id']);
        $barcode = new TCPDF2DBarcode($verificationUrl, 'QRCODE,H');
        $png = $barcode->getBarcodePngData(8, 8, [0, 0, 0]);

        header('Content-Type: image/png');
        header('Content-Length: ' . strlen($png));
        header('Cache-Control: private, max-age=3600');
        header('X-Content-Type-Options: nosniff');
        echo $png;
        exit;
    }

}
