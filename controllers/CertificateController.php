<?php

class CertificateController extends Controller
{
    public function index()
    {
        $this->authCheck();

        $certificateModel = $this->model('Certificate');
        $userId = $_SESSION['user']['id'];

        $data['certificates'] = $certificateModel->getCertificatesByUserId($userId);
        $data['title'] = 'Sertifikat Digital - Tandain';
        $data['active_menu'] = 'certificate';
        $data['user'] = $_SESSION['user'];
        $data['csrf_token'] = $this->getCsrfToken();

        $this->view('certificate/index', $data);
    }

    public function create()
    {
        $this->authCheck();

        $data['title'] = 'Buat Sertifikat Baru - Tandain';
        $data['active_menu'] = 'certificate_create';
        $data['user'] = $_SESSION['user'];
        $data['csrf_token'] = $this->getCsrfToken();

        $this->view('certificate/create', $data);
    }

    public function sign()
    {
        $this->authCheck();

        $userModel = $this->model('User');

        $data['title'] = 'Gambar Tanda Tangan - Tandain';
        $data['active_menu'] = 'signature_image';
        $data['user'] = $userModel->findById($_SESSION['user']['id']);
        $data['csrf_token'] = $this->getCsrfToken();

        $this->view('certificate/sign', $data);
    }

    public function store()
    {
        $this->authCheck();

        $redirectUrl = '/Tandain/certificate/create';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            $passphrase = $_POST['passphrase'] ?? '';
            $passphraseConfirmation = $_POST['passphrase_confirmation'] ?? '';

            if (!$this->verifyCsrfToken($csrfToken)) {
                $this->setFlash('error', 'Sesi formulir tidak valid. Silakan coba kembali.');
            } elseif (!is_string($passphrase) || !is_string($passphraseConfirmation) || strlen(trim($passphrase)) < 4) {
                $this->setFlash('error', 'Passphrase minimal terdiri dari 4 karakter.');
            } elseif ($passphrase !== $passphraseConfirmation) {
                $this->setFlash('error', 'Konfirmasi passphrase tidak cocok.');
            } else {
                $certificateModel = $this->model('Certificate');
                $userId = $_SESSION['user']['id'];
                $keypair = null;

                try {
                    $keypair = $certificateModel->generateRSA($passphrase);
                } catch (RuntimeException $exception) {
                    error_log('Certificate RSA generation failed: ' . $exception->getMessage());
                    $this->setFlash('error', 'Gagal menerbitkan kunci sertifikat digital. Silakan coba lagi.');
                }

                if ($keypair !== null) {
                    $serialNumber = 'CERT-PNJ-TIK-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                    $validFrom = date('c');
                    $validUntil = date('c', strtotime('+1 year'));

                    $created = $certificateModel->createCertificate(
                        $userId,
                        $keypair['private_key'],
                        $keypair['public_key'],
                        $serialNumber,
                        $validFrom,
                        $validUntil
                    );

                    if ($created) {
                        $this->setFlash('success', 'Sertifikat digital baru berhasil diterbitkan dan diaktifkan.');
                        $redirectUrl = '/Tandain/certificate';
                    } else {
                        $this->setFlash('error', 'Gagal menyimpan sertifikat digital ke database.');
                    }
                }
            }
        }

        $this->redirect($redirectUrl);
    }

    public function revoke()
    {
        $this->authCheck();

        $certificateIdInput = $_POST['certificate_id'] ?? '';
        $certificateId = is_string($certificateIdInput) ? trim($certificateIdInput) : '';
        $csrfToken = $_POST['csrf_token'] ?? '';

        if (
            $_SERVER['REQUEST_METHOD'] !== 'POST'
            || !isValidUuid($certificateId)
            || !$this->verifyCsrfToken($csrfToken)
        ) {
            $this->setFlash('error', 'Permintaan pencabutan sertifikat tidak valid.');
        } else {
            $certificateModel = $this->model('Certificate');
            $userId = $_SESSION['user']['id'];

            try {
                $revoked = $certificateModel->revokeCertificate($userId, $certificateId);

                if ($revoked) {
                    $this->setFlash('success', 'Sertifikat digital berhasil dicabut.');
                } else {
                    $this->setFlash('error', 'Sertifikat tidak ditemukan atau sudah tidak aktif.');
                }
            } catch (Throwable $exception) {
                error_log('Certificate revocation failed for certificate ' . $certificateId . ': ' . $exception->getMessage());
                $this->setFlash('error', 'Terjadi kesalahan saat mencabut sertifikat.');
            }
        }

        $this->redirect('/Tandain/certificate');
    }

    public function updateSignature()
    {
        header('Content-Type: application/json; charset=utf-8');

        $statusCode = 200;
        $payload = [];

        if (!$this->refreshAuthenticatedUser()) {
            $statusCode = 401;
            $payload = [
                'message' => 'Sesi Anda telah berakhir. Silakan masuk kembali.',
                'redirect' => '/Tandain/login',
            ];
        } elseif ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $statusCode = 405;
            $payload = ['message' => 'Metode permintaan tidak diizinkan.'];
        } else {
            $csrfToken = $_POST['csrf_token'] ?? '';
            $file = $_FILES['signature_file'] ?? null;

            if (!$this->verifyCsrfToken($csrfToken)) {
                $statusCode = 403;
                $payload = ['message' => 'Sesi tidak valid. Muat ulang halaman lalu coba lagi.'];
            } elseif (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
                $statusCode = 400;
                $payload = ['message' => 'Pilih berkas PNG atau buat tanda tangan pada kanvas.'];
            } elseif ($file['size'] <= 0 || $file['size'] > 2 * 1024 * 1024) {
                $statusCode = 400;
                $payload = ['message' => 'Ukuran berkas maksimal 2 MB.'];
            } else {
                $imageInfo = @getimagesize($file['tmp_name']);
                $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);

                if ($mimeType !== 'image/png' || !$imageInfo || $imageInfo[2] !== IMAGETYPE_PNG) {
                    $statusCode = 400;
                    $payload = ['message' => 'Berkas harus berupa gambar PNG yang valid.'];
                } else {
                    $uploadDirectory = 'uploads/signatures';

                    if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
                        $statusCode = 500;
                        $payload = ['message' => 'Gagal menyiapkan penyimpanan tanda tangan.'];
                    } else {
                        $fileName = bin2hex(random_bytes(16)) . '.png';
                        $destination = $uploadDirectory . '/' . $fileName;

                        if (!move_uploaded_file($file['tmp_name'], $destination)) {
                            $statusCode = 500;
                            $payload = ['message' => 'Gagal menyimpan berkas tanda tangan.'];
                        } else {
                            $userModel = $this->model('User');
                            $updated = false;
                            $oldPath = '';

                            try {
                                $user = $userModel->findById($_SESSION['user']['id']);

                                if ($user) {
                                    $oldPath = (string) ($user['signature_image_path'] ?? '');
                                    $updated = $userModel->updateSignatureImagePath($_SESSION['user']['id'], $destination);
                                }
                            } catch (Throwable $exception) {
                                error_log('Failed to update signature image path: ' . $exception->getMessage());
                            }

                            if (!$updated) {
                                @unlink($destination);
                                $statusCode = 500;
                                $payload = ['message' => 'Gagal menyimpan tanda tangan ke akun.'];
                            } else {
                                if ($oldPath !== '' && str_starts_with($oldPath, 'uploads/signatures/') && is_file($oldPath)) {
                                    @unlink($oldPath);
                                }

                                $payload = [
                                    'message' => 'Tanda tangan berhasil disimpan.',
                                    'image_url' => '/Tandain/' . $destination . '?v=' . filemtime($destination),
                                ];
                            }
                        }
                    }
                }
            }
        }

        http_response_code($statusCode);
        echo json_encode($payload);
    }

    public function qr($documentId = null)
    {
        $this->authCheck();

        $documentSignerModel = $this->model('DocumentSigner');
        $document = null;

        if (isValidUuid($documentId)) {
            $document = $documentSignerModel->getDetailDocumentById($documentId, $_SESSION['user']['id']);
        }

        if (!$document) {
            http_response_code(404);
        } else {
            require_once 'vendor/tecnickcom/tcpdf/tcpdf_barcodes_2d.php';

            $environment = is_file('.env') ? (parse_ini_file('.env') ?: []) : [];
            $appUrl = trim($environment['APP_URL'] ?? '');

            if ($appUrl !== '') {
                $appBaseUrl = rtrim($appUrl, '/');
            } else {
                $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $appBaseUrl = $scheme . '://' . $host . '/Tandain';
            }

            $verificationUrl = $appBaseUrl . '/verify/' . rawurlencode($document['id']);
            $barcode = new TCPDF2DBarcode($verificationUrl, 'QRCODE,H');
            $png = $barcode->getBarcodePngData(8, 8, [0, 0, 0]);

            header('Content-Type: image/png');
            header('Content-Length: ' . strlen($png));
            header('Cache-Control: private, max-age=3600');
            header('X-Content-Type-Options: nosniff');
            echo $png;
        }

        exit;
    }
}
