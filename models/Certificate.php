<?php

class Certificate
{
    private $table = 'signer_certificates';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getCertificatesByUserId($userId)
    {
        $sql = "SELECT certificates.*, users.identity_number FROM " . $this->table . " AS certificates JOIN users ON users.id = certificates.user_id WHERE certificates.user_id = :user_id ORDER BY certificates.created_at DESC";

        $this->db->query($sql);
        $this->db->bind('user_id', $userId);

        return $this->db->resultSet();
    }

    public function generateRSA($passphrase): array
    {
        $config = [
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $configCandidates = [];
        $envConfig = getenv('OPENSSL_CONF');

        if (is_string($envConfig) && $envConfig !== '') {
            $configCandidates[] = $envConfig;
        }

        $loadedIni = php_ini_loaded_file();

        if ($loadedIni !== false) {
            $configCandidates[] = dirname($loadedIni) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf';
        }

        $configCandidates[] = dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . 'extras' . DIRECTORY_SEPARATOR . 'ssl' . DIRECTORY_SEPARATOR . 'openssl.cnf';
        $configCandidates[] = dirname(PHP_BINARY) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'conf' . DIRECTORY_SEPARATOR . 'openssl.cnf';
        $configCandidates[] = 'C:/xampp/php/extras/ssl/openssl.cnf';
        $configCandidates[] = 'C:/xampp/apache/conf/openssl.cnf';

        foreach ($configCandidates as $candidate) {
            if (is_file($candidate)) {
                $config['config'] = $candidate;
                break;
            }
        }

        $key = openssl_pkey_new($config);

        if ($key === false) {
            $error = openssl_error_string();
            throw new RuntimeException('Failed to generate RSA key' . ($error ? ': ' . $error : '.'));
        }

        $privateKey = '';

        if (!openssl_pkey_export($key, $privateKey, $passphrase, $config)) {
            $error = openssl_error_string();
            throw new RuntimeException('Failed to export encrypted RSA private key' . ($error ? ': ' . $error : '.'));
        }

        $details = openssl_pkey_get_details($key);

        if ($details === false || !isset($details['key'])) {
            $error = openssl_error_string();
            throw new RuntimeException('Failed to read RSA public key details' . ($error ? ': ' . $error : '.'));
        }

        return [
            'private_key' => $privateKey,
            'public_key' => $details['key'],
        ];
    }

    public function createCertificate($userId, $privateKey, $publicKey, $certificateSerial, $validFrom, $validUntil)
    {
        $this->db->query("SELECT COUNT(*) AS count FROM " . $this->table . " WHERE user_id = :user_id AND status = 'ACTIVE'");
        $this->db->bind('user_id', $userId);
        $activeCertificates = $this->db->single();

        if (($activeCertificates['count'] ?? 0) > 0) {
            $this->db->query("UPDATE " . $this->table . " SET status = 'REVOKED', revoked_at = CURRENT_TIMESTAMP WHERE user_id = :user_id AND status = 'ACTIVE'");
            $this->db->bind('user_id', $userId);
            $this->db->execute();
        }

        $sql = "INSERT INTO " . $this->table . " (user_id, certificate_serial, public_key, encrypted_private_key, valid_from, valid_until, status) VALUES (:user_id, :certificate_serial, :public_key, :encrypted_private_key, :valid_from, :valid_until, 'ACTIVE')";

        $this->db->query($sql);
        $this->db->bind('user_id', $userId);
        $this->db->bind('certificate_serial', $certificateSerial);
        $this->db->bind('public_key', $publicKey);
        $this->db->bind('encrypted_private_key', $privateKey);
        $this->db->bind('valid_from', $validFrom);
        $this->db->bind('valid_until', $validUntil);

        return $this->db->execute();
    }

    public function revokeCertificate($userId, $certificateId)
    {
        $revoked = false;

        if (is_string($certificateId) && preg_match('/\A[0-9a-f]{8}-(?:[0-9a-f]{4}-){3}[0-9a-f]{12}\z/i', $certificateId)) {
            $sql = "UPDATE " . $this->table . " SET status = 'REVOKED', revoked_at = CURRENT_TIMESTAMP WHERE id = :certificate_id AND user_id = :user_id AND status = 'ACTIVE'";

            $this->db->query($sql);
            $this->db->bind('user_id', $userId);
            $this->db->bind('certificate_id', $certificateId);

            $revoked = $this->db->execute() && $this->db->rowCount() === 1;
        }

        return $revoked;
    }
}
