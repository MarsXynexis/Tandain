<?php

require_once 'core/PDFStamper.php';

class DocumentSigner
{
    private $table = 'document_signers';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getDocumentsBySignerId($userId)
    {
        $sql = "SELECT DISTINCT
            ds.id AS signer_row_id,
            ds.signing_order,
            ds.status AS signer_status,
            ds.stamp_type AS signer_stamp_type,
            ds.page_number AS signer_page_number,
            ds.coord_x AS signer_coord_x,
            ds.coord_y AS signer_coord_y,
            d.id AS document_id,
            d.document_number,
            d.title AS document_title,
            d.status AS document_status,
            d.routing_type AS document_routing_type,
            d.created_at AS submitted_at,
            req.id AS requester_id,
            req.full_name AS requester_name,
            req.identity_number AS requester_nim_nip,
            requester_role.name AS requester_role_name,
            req.email AS requester_email,
            req.position AS requester_position,
            CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END AS has_active_cert
        FROM " . $this->table . " ds
        JOIN documents d ON d.id = ds.document_id
        JOIN users req ON req.id = d.requester_id
        JOIN roles requester_role ON requester_role.id = req.role_id
        LEFT JOIN signer_certificates sc ON sc.user_id = ds.signer_id AND sc.status = 'ACTIVE'
        WHERE ds.signer_id = :signer_id
          AND NOT ds.status = 'WAITING'
          AND d.status IN ('IN_PROGRESS', 'COMPLETED', 'REJECTED')
        ORDER BY submitted_at DESC";


        $this->db->query($sql);
        $this->db->bind('signer_id', $userId);

        return $this->db->resultSet();
    }

    public function getSignerByDocumentId($documentId)
    {
        $sql = "SELECT
            ds.*,
            u.full_name AS signer_name,
            u.signature_image_path AS signer_signature_image_path,
            d.original_file_path AS original_file_path,
            u.identity_number AS signer_nim_nip,
            u.email AS signer_email,
            u.position AS signer_position,
            CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END AS has_active_cert
        FROM " . $this->table . " ds
        JOIN users u ON u.id = ds.signer_id
        JOIN documents d ON d.id = ds.document_id
        LEFT JOIN signer_certificates sc
            ON sc.user_id = u.id
           AND sc.status = 'ACTIVE'
           AND sc.valid_from <= CURRENT_TIMESTAMP
           AND sc.valid_until >= CURRENT_TIMESTAMP
        WHERE ds.document_id = :document_id
        ORDER BY ds.signing_order ASC";

        $this->db->query($sql);
        $this->db->bind('document_id', $documentId);

        return $this->db->resultSet();
    }

    public function getByDocumentId($documentId)
    {
        return $this->getSignerByDocumentId($documentId);
    }

    public function getDetailDocumentById($documentId, $signerId)
    {
        $sql = "SELECT
            d.*,
            d.created_at AS submitted_at,
            req.full_name AS requester_name,
            req.identity_number AS requester_nim,
            req.email AS requester_email,
            req.position AS requester_position,
            ds.status AS signer_status,
            ds.stamp_type AS signer_stamp_type
        FROM documents d
        JOIN document_signers ds ON ds.document_id = d.id
        JOIN users req ON req.id = d.requester_id
        WHERE d.id = :document_id
          AND ds.signer_id = :signer_id
        LIMIT 1";

        $this->db->query($sql);
        $this->db->bind('document_id', $documentId);
        $this->db->bind('signer_id', $signerId);

        return $this->db->single();
    }

    public function updateStampType($documentId, $signerId, $stampType)
    {
        $this->db->query(
            "UPDATE " . $this->table . "
             SET stamp_type = :stamp_type
             WHERE document_id = :document_id
               AND signer_id = :signer_id
               AND status = 'CURRENT_TURN'"
        );
        $this->db->bind('stamp_type', $stampType);
        $this->db->bind('document_id', $documentId);
        $this->db->bind('signer_id', $signerId);

        return $this->db->execute() && $this->db->rowCount() > 0;
    }

    public function agreeDocument($documentId, $signerId, $passphrase)
    {
        if (
            !is_string($documentId)
            || !preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/i', $documentId)
            || !is_string($signerId)
            || !preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/i', $signerId)
        ) {
            return ['ok' => false, 'message' => 'Identitas dokumen atau penandatangan tidak valid.'];
        }

        if (!is_string($passphrase) || $passphrase === '') {
            return ['ok' => false, 'message' => 'Passphrase sertifikat wajib diisi.'];
        }

        if (
            !function_exists('openssl_pkey_get_private')
            || !function_exists('openssl_sign')
            || !function_exists('openssl_verify')
        ) {
            return ['ok' => false, 'message' => 'Fitur tanda tangan digital tidak tersedia di server.'];
        }

        $pdo = $this->db->getConnection();
        $newPdfAbsolutePath = null;

        try {
            $pdo->beginTransaction();

            $documentQuery = $pdo->prepare(
                "SELECT id, status, original_file_path, final_file_path,
                        initial_sha256_hash, final_sha256_hash, is_sequential
                 FROM documents
                 WHERE id = ?
                 FOR UPDATE"
            );
            $documentQuery->execute([$documentId]);
            $document = $documentQuery->fetch(PDO::FETCH_ASSOC);

            if (!$document || $document['status'] !== 'IN_PROGRESS') {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Dokumen tidak sedang menunggu tanda tangan.'];
            }

            $signerQuery = $pdo->prepare(
                "SELECT id, signing_order, status, stamp_type, page_number, coord_x, coord_y
                 FROM document_signers
                 WHERE document_id = ? AND signer_id = ?
                 FOR UPDATE"
            );
            $signerQuery->execute([$documentId, $signerId]);
            $signer = $signerQuery->fetch(PDO::FETCH_ASSOC);

            if (!$signer || $signer['status'] !== 'CURRENT_TURN') {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Dokumen tidak sedang menunggu giliran Anda.'];
            }

            $userQuery = $pdo->prepare(
                "SELECT signature_image_path
                 FROM users
                 WHERE id = ? AND status = 'ACTIVE'
                 FOR UPDATE"
            );
            $userQuery->execute([$signerId]);
            $user = $userQuery->fetch(PDO::FETCH_ASSOC);
            $signaturePath = $user['signature_image_path'] ?? '';

            if (
                !is_string($signaturePath)
                || !preg_match('/\Auploads\/signatures\/[A-Za-z0-9._-]+\.png\z/i', $signaturePath)
            ) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Gambar tanda tangan aktif belum tersedia. Unggah tanda tangan terlebih dahulu.'];
            }

            $signatureFile = dirname(__DIR__) . DIRECTORY_SEPARATOR
                . str_replace('/', DIRECTORY_SEPARATOR, $signaturePath);
            $signatureImageInfo = is_file($signatureFile) ? @getimagesize($signatureFile) : false;
            if (!$signatureImageInfo || $signatureImageInfo[2] !== IMAGETYPE_PNG) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Berkas gambar tanda tangan tidak valid atau tidak ditemukan. Unggah ulang tanda tangan Anda.'];
            }
            $signatureImageHash = hash_file('sha256', $signatureFile);
            if (!is_string($signatureImageHash)) {
                throw new RuntimeException('Unable to calculate the signature image hash.');
            }

            $hasFinalFile = is_string($document['final_file_path']) && $document['final_file_path'] !== '';
            $relativePath = $hasFinalFile ? $document['final_file_path'] : $document['original_file_path'];
            $sourceHash = $hasFinalFile ? $document['final_sha256_hash'] : $document['initial_sha256_hash'];
            $previousSignedPath = null;

            if ($hasFinalFile && preg_match('/\Auploads\/signed\/[A-Za-z0-9._-]+\.pdf\z/i', $document['final_file_path'])) {
                $previousSignedPath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                    . str_replace('/', DIRECTORY_SEPARATOR, $document['final_file_path']);
            }

            if (
                !is_string($relativePath)
                || !preg_match('/\Auploads\/(?:documents|signed)\/[A-Za-z0-9._-]+\.pdf\z/i', $relativePath)
                || !is_string($sourceHash)
                || !preg_match('/\A[a-f0-9]{64}\z/i', $sourceHash)
            ) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Berkas dokumen tidak valid.'];
            }

            $documentPath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
            $actualHash = is_file($documentPath) ? hash_file('sha256', $documentPath) : false;

            if (!is_string($actualHash) || !hash_equals($sourceHash, $actualHash)) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Berkas dokumen tidak tersedia atau telah berubah.'];
            }

            $certificateQuery = $pdo->prepare(
                "SELECT id, certificate_serial, public_key, encrypted_private_key
                 FROM signer_certificates
                 WHERE user_id = ?
                   AND status = 'ACTIVE'
                   AND valid_from <= CURRENT_TIMESTAMP
                   AND valid_until >= CURRENT_TIMESTAMP
                 ORDER BY created_at DESC
                 LIMIT 1
                 FOR UPDATE"
            );
            $certificateQuery->execute([$signerId]);
            $certificate = $certificateQuery->fetch(PDO::FETCH_ASSOC);

            if (!$certificate) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Sertifikat aktif tidak ditemukan atau sudah kedaluwarsa.'];
            }

            $privateKey = openssl_pkey_get_private($certificate['encrypted_private_key'], $passphrase);
            if ($privateKey === false) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Passphrase sertifikat tidak benar.'];
            }

            $privateDetails = openssl_pkey_get_details($privateKey);
            $publicKey = openssl_pkey_get_public($certificate['public_key']);
            $publicDetails = $publicKey === false ? false : openssl_pkey_get_details($publicKey);

            if (
                $privateDetails === false
                || $publicDetails === false
                || !isset($privateDetails['key'], $publicDetails['key'])
                || !hash_equals(trim($publicDetails['key']), trim($privateDetails['key']))
            ) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Kunci sertifikat tidak cocok. Hubungi administrator.'];
            }

            $previousSignatureQuery = $pdo->prepare(
                "SELECT signed_hash
                 FROM document_signers
                 WHERE document_id = ? AND status = 'SIGNED' AND signed_hash IS NOT NULL
                 ORDER BY signed_at DESC, signing_order DESC
                 LIMIT 1
                 FOR UPDATE"
            );
            $previousSignatureQuery->execute([$documentId]);
            $previousHash = $previousSignatureQuery->fetchColumn();
            $parentHash = $previousHash === false ? $document['initial_sha256_hash'] : $previousHash;

            $signedAt = $pdo->query('SELECT CURRENT_TIMESTAMP')->fetchColumn();
            $payload = json_encode([
                'version' => 1,
                'document_id' => $documentId,
                'document_sha256' => $document['initial_sha256_hash'],
                'parent_hash' => $parentHash,
                'signer_id' => $signerId,
                'certificate_serial' => $certificate['certificate_serial'],
                'signature_image_sha256' => $signatureImageHash,
                'signed_at' => $signedAt,
                'signing_order' => (int) $signer['signing_order'],
                'stamp_type' => $signer['stamp_type'],
                'page_number' => (int) $signer['page_number'],
                'coord_x' => sprintf('%.12F', (float) $signer['coord_x']),
                'coord_y' => sprintf('%.12F', (float) $signer['coord_y']),
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

            if (!openssl_sign($payload, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Tanda tangan digital gagal dibuat. Silakan coba lagi.'];
            }

            if (openssl_verify($payload, $signature, $publicKey, OPENSSL_ALGO_SHA256) !== 1) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Tanda tangan digital gagal diverifikasi. Silakan coba lagi.'];
            }

            $newPdfRelativePath = PDFStamper::stamp(
                $documentPath,
                $signatureFile,
                $documentId,
                (int) $signer['signing_order'],
                (string) $signer['stamp_type'],
                (int) $signer['page_number'],
                (float) $signer['coord_x'],
                (float) $signer['coord_y']
            );
            $newPdfAbsolutePath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                . str_replace('/', DIRECTORY_SEPARATOR, $newPdfRelativePath);
            $newPdfHash = hash_file('sha256', $newPdfAbsolutePath);
            if (!is_string($newPdfHash)) {
                throw new RuntimeException('Unable to calculate the stamped PDF hash.');
            }

            $signatureValue = json_encode([
                'algorithm' => 'RSA-SHA256',
                'payload' => base64_encode($payload),
                'signature' => base64_encode($signature),
            ], JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

            $updateSigner = $pdo->prepare(
                "UPDATE document_signers
                 SET status = 'SIGNED',
                     certificate_id = ?,
                     parent_hash = ?,
                     signed_hash = ?,
                     signature_value = ?,
                     signed_at = ?
                 WHERE id = ? AND status = 'CURRENT_TURN'"
            );
            $updateSigner->execute([
                $certificate['id'],
                $parentHash,
                hash('sha256', $payload),
                $signatureValue,
                $signedAt,
                $signer['id'],
            ]);

            if ($updateSigner->rowCount() !== 1) {
                throw new RuntimeException('Signer status changed while signing.');
            }

            $isSequential = in_array($document['is_sequential'], [true, 1, '1', 't', 'true'], true);
            if ($isSequential) {
                $nextSignerQuery = $pdo->prepare(
                    "SELECT id
                     FROM document_signers
                     WHERE document_id = ? AND status = 'WAITING'
                     ORDER BY signing_order ASC
                     LIMIT 1
                     FOR UPDATE"
                );
                $nextSignerQuery->execute([$documentId]);
                $nextSignerId = $nextSignerQuery->fetchColumn();

                if ($nextSignerId !== false) {
                    $pdo->prepare(
                        "UPDATE document_signers SET status = 'CURRENT_TURN' WHERE id = ? AND status = 'WAITING'"
                    )->execute([$nextSignerId]);
                }
            }

            $remainingQuery = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM document_signers
                 WHERE document_id = ? AND status <> 'SIGNED'"
            );
            $remainingQuery->execute([$documentId]);
            $completed = (int) $remainingQuery->fetchColumn() === 0;

            $updateDocument = $pdo->prepare(
                "UPDATE documents
                 SET final_file_path = ?, final_sha256_hash = ?
                 WHERE id = ? AND status = 'IN_PROGRESS'"
            );
            $updateDocument->execute([$newPdfRelativePath, $newPdfHash, $documentId]);
            if ($updateDocument->rowCount() !== 1) {
                throw new RuntimeException('Document file path changed while signing.');
            }

            if ($completed) {
                $completeDocument = $pdo->prepare(
                    "UPDATE documents
                     SET status = 'COMPLETED',
                         completed_at = CURRENT_TIMESTAMP
                     WHERE id = ? AND status = 'IN_PROGRESS'"
                );
                $completeDocument->execute([$documentId]);
                if ($completeDocument->rowCount() !== 1) {
                    throw new RuntimeException('Document status changed while completing signatures.');
                }
            }

            $pdo->prepare(
                "INSERT INTO document_audit_logs (document_id, user_id, action, notes)
                 VALUES (?, ?, 'DOCUMENT_SIGNED', ?)"
            )->execute([
                $documentId,
                $signerId,
                'Tanda tangan digital RSA-SHA256 dibubuhkan menggunakan sertifikat '
                    . $certificate['certificate_serial'] . ' dan stempel visual PDF diperbarui.',
            ]);

            $pdo->commit();

            if ($previousSignedPath !== null && $previousSignedPath !== $newPdfAbsolutePath && is_file($previousSignedPath)) {
                @unlink($previousSignedPath);
            }

            return ['ok' => true, 'completed' => $completed];
        } catch (Throwable $exception) {
            try {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
            } finally {
                if ($newPdfAbsolutePath !== null && is_file($newPdfAbsolutePath)) {
                    @unlink($newPdfAbsolutePath);
                }
            }

            throw $exception;
        }
    }

    public function rejectDocument($documentId, $signerId, $reason)
    {
        if (
            !is_string($documentId)
            || !preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/i', $documentId)
            || !is_string($signerId)
            || !preg_match('/\A[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}\z/i', $signerId)
        ) {
            return ['ok' => false, 'message' => 'Identitas dokumen atau penandatangan tidak valid.'];
        }

        if (!is_string($reason) || trim($reason) === '' || strlen(trim($reason)) > 1000) {
            return ['ok' => false, 'message' => 'Alasan penolakan wajib diisi dan maksimal 1000 karakter.'];
        }

        $reason = trim($reason);
        $pdo = $this->db->getConnection();

        try {
            $pdo->beginTransaction();

            $documentQuery = $pdo->prepare(
                "SELECT id, status, final_file_path
                 FROM documents
                 WHERE id = ?
                 FOR UPDATE"
            );
            $documentQuery->execute([$documentId]);
            $document = $documentQuery->fetch(PDO::FETCH_ASSOC);

            if (!$document || $document['status'] !== 'IN_PROGRESS') {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Dokumen tidak sedang menunggu keputusan.'];
            }

            $partialSignedPath = null;

            if (is_string($document['final_file_path'] ?? null) && preg_match('/\Auploads\/signed\/[A-Za-z0-9._-]+\.pdf\z/i', $document['final_file_path'])) {
                $partialSignedPath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                    . str_replace('/', DIRECTORY_SEPARATOR, $document['final_file_path']);
            }

            $signerQuery = $pdo->prepare(
                "SELECT id
                 FROM document_signers
                 WHERE document_id = ? AND signer_id = ? AND status = 'CURRENT_TURN'
                 FOR UPDATE"
            );
            $signerQuery->execute([$documentId, $signerId]);
            $signer = $signerQuery->fetch(PDO::FETCH_ASSOC);

            if (!$signer) {
                $pdo->rollBack();
                return ['ok' => false, 'message' => 'Dokumen tidak sedang menunggu giliran Anda.'];
            }

            $rejectSigner = $pdo->prepare(
                "UPDATE document_signers
                 SET status = 'REJECTED', rejection_reason = ?
                 WHERE id = ? AND status = 'CURRENT_TURN'"
            );
            $rejectSigner->execute([$reason, $signer['id']]);

            if ($rejectSigner->rowCount() !== 1) {
                throw new RuntimeException('Signer status changed while rejecting the document.');
            }

            $pdo->prepare(
                "UPDATE document_signers
                 SET status = 'CANCELLED'
                 WHERE document_id = ?
                   AND id <> ?
                   AND status IN ('WAITING', 'CURRENT_TURN')"
            )->execute([$documentId, $signer['id']]);

            $rejectDocument = $pdo->prepare(
                "UPDATE documents
                 SET status = 'REJECTED', rejection_reason = ?, final_file_path = NULL, final_sha256_hash = NULL
                 WHERE id = ? AND status = 'IN_PROGRESS'"
            );
            $rejectDocument->execute([$reason, $documentId]);

            if ($rejectDocument->rowCount() !== 1) {
                throw new RuntimeException('Document status changed while rejecting.');
            }

            $pdo->prepare(
                "INSERT INTO document_audit_logs (document_id, user_id, action, notes)
                 VALUES (?, ?, 'REJECTED', ?)"
            )->execute([$documentId, $signerId, 'Dokumen ditolak oleh penandatangan. Alasan: ' . $reason]);

            $pdo->commit();

            if ($partialSignedPath !== null && is_file($partialSignedPath)) {
                @unlink($partialSignedPath);
            }

            return ['ok' => true];
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $exception;
        }
    }



    public function createSigner($data)
    {
        $sql = "INSERT INTO " . $this->table . " (document_id, signer_id, signing_order, status, stamp_type, page_number, coord_x, coord_y) VALUES (:document_id, :signer_id, :signing_order, :status, :stamp_type, :page_number, :coord_x, :coord_y)";

        $this->db->query($sql);

        $this->db->bind('document_id', $data['document_id']);
        $this->db->bind('signer_id', $data['signer_id']);
        $this->db->bind('signing_order', $data['signing_order']);
        $this->db->bind('status', $data['status'] ?? 'WAITING');
        $this->db->bind('stamp_type', $data['stamp_type'] ?? 'DUAL_DISPLAY');
        $this->db->bind('page_number', $data['page_number'] ?? 1);
        $this->db->bind('coord_x', $data['coord_x'] ?? 0);
        $this->db->bind('coord_y', $data['coord_y'] ?? 0);

        return $this->db->execute();
    }

    public function deleteByDocumentId($documentId)
    {
        $this->db->query("DELETE FROM " . $this->table . " WHERE document_id = :document_id");

        $this->db->bind('document_id', $documentId);

        return $this->db->execute();
    }
}
