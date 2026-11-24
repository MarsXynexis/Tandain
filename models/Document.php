<?php

require_once 'core/PDFStamper.php';

class Document
{
    private $table = 'documents';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function findById($id)
    {
        $document = false;

        if (isValidUuid($id)) {
            $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1");

            $this->db->bind('id', $id);

            $document = $this->db->single();
        }

        return $document;
    }

    public function findByIdWithRequester($id)
    {
        $document = false;

        if (isValidUuid($id)) {
            $sql = "SELECT d.*,
                           u.full_name AS requester_name,
                           u.identity_number AS requester_nim,
                           u.identity_number AS requester_nim_nip,
                           u.email AS requester_email,
                           u.position AS requester_position,
                           u.study_program AS requester_study_program,
                           requester_role.name AS requester_role_name,
                           requester_role.display_name AS requester_role_display_name,
                           t.full_name AS triage_admin_name
                    FROM " . $this->table . " d
                    JOIN users u ON u.id = d.requester_id
                    LEFT JOIN roles requester_role ON requester_role.id = u.role_id
                    LEFT JOIN users t ON t.id = d.triage_handled_by
                    WHERE d.id = :id
                    LIMIT 1";

            $this->db->query($sql);

            $this->db->bind('id', $id);

            $document = $this->db->single();
        }

        return $document;
    }

    public function getPendingTriageDocuments($status = 'pending', $search = '')
    {
        $sql = "SELECT d.id, d.document_number, d.title, d.status, d.routing_type, d.original_file_path, d.created_at, u.full_name AS requester_name, u.identity_number AS requester_nim, u.email AS requester_email, u.position AS requester_position, u.study_program AS requester_study_program, requester_role.display_name AS requester_role_display_name FROM " . $this->table . " d JOIN users u ON u.id = d.requester_id LEFT JOIN roles requester_role ON requester_role.id = u.role_id LEFT JOIN document_signers ds_rej ON ds_rej.document_id = d.id AND ds_rej.status = 'REJECTED' WHERE ";

        if ($status === 'dispatched') {
            $sql .= "(d.status = 'IN_PROGRESS' AND d.triage_handled_by IS NOT NULL)";
        } else if ($status === 'rejected') {
            $sql .= "(d.status = 'REJECTED' AND d.triage_handled_by IS NOT NULL AND ds_rej.id IS NULL)";
        } else if ($status === 'all') {
            $sql .= "(d.status = 'PENDING_TRIAGE' OR (d.status = 'IN_PROGRESS' AND d.triage_handled_by IS NOT NULL) OR (d.status = 'REJECTED' AND d.triage_handled_by IS NOT NULL AND ds_rej.id IS NULL))";
        } else {
            $sql .= "d.status = 'PENDING_TRIAGE'";
        }

        if (!empty($search)) {
            $sql .= " AND (d.title ILIKE :search OR u.full_name ILIKE :search OR u.identity_number ILIKE :search)";
        }

        $sql .= " ORDER BY d.created_at DESC";

        $this->db->query($sql);

        if (!empty($search)) {
            $this->db->bind('search', '%' . $search . '%');
        }

        return $this->db->resultSet();
    }

    public function getTriageStatistics()
    {
        $sql = "SELECT COUNT(CASE WHEN d.status = 'PENDING_TRIAGE' THEN 1 END) AS pending_count, COUNT(CASE WHEN d.status = 'IN_PROGRESS' AND d.triage_handled_by IS NOT NULL THEN 1 END) AS dispatched_count, COUNT(CASE WHEN d.status = 'REJECTED' AND d.triage_handled_by IS NOT NULL AND ds_rej.id IS NULL THEN 1 END) AS rejected_count, COUNT(CASE WHEN d.status = 'PENDING_TRIAGE' OR (d.status = 'IN_PROGRESS' AND d.triage_handled_by IS NOT NULL) OR (d.status = 'REJECTED' AND d.triage_handled_by IS NOT NULL AND ds_rej.id IS NULL) THEN 1 END) AS total_triage FROM " . $this->table . " d LEFT JOIN document_signers ds_rej ON ds_rej.document_id = d.id AND ds_rej.status = 'REJECTED'";

        $this->db->query($sql);

        return $this->db->single();
    }

    public function applyTriageDisposition($id, $adminId, $documentNumber, $signersList, $isSequential = true, $expiresAt = null)
    {
        try {
            $this->db->query("BEGIN");
            $this->db->execute();

            $sqlUpdate = "UPDATE " . $this->table . " SET triage_handled_by = :admin_id, document_number = :document_number, is_sequential = :is_sequential, issued_at = CURRENT_TIMESTAMP, expires_at = :expires_at, updated_at = CURRENT_TIMESTAMP WHERE id = :id AND status = 'PENDING_TRIAGE'";

            $this->db->query($sqlUpdate);
            $this->db->bind('id', $id);
            $this->db->bind('admin_id', $adminId);
            $this->db->bind('document_number', !empty($documentNumber) ? $documentNumber : null);
            $this->db->bind('is_sequential', $isSequential ? 'true' : 'false');
            $this->db->bind('expires_at', !empty($expiresAt) ? $expiresAt : null);
            $this->db->execute();

            if ($this->db->rowCount() === 0) {
                throw new Exception('Dokumen tidak berada pada antrean disposisi.');
            }

            $this->db->query("DELETE FROM document_signers WHERE document_id = :doc_id");
            $this->db->bind('doc_id', $id);
            $this->db->execute();

            $order = 1;

            foreach ($signersList as $signer) {
                $stampType = $signer['stamp_type'] ?? 'DUAL_DISPLAY';

                $sqlInsertSigner = "INSERT INTO document_signers (document_id, signer_id, signing_order, status, stamp_type) VALUES (:doc_id, :signer_id, :signing_order, 'WAITING', :stamp_type)";

                $this->db->query($sqlInsertSigner);
                $this->db->bind('doc_id', $id);
                $this->db->bind('signer_id', $signer['signer_id']);
                $this->db->bind('signing_order', $order);
                $this->db->bind('stamp_type', $stampType);
                $this->db->execute();

                $order++;
            }

            if (!empty($documentNumber)) {
                $auditNotes = 'Dokumen didisposisikan oleh Tata Usaha dengan nomor agenda ' . $documentNumber;
            } else {
                $auditNotes = 'Dokumen didisposisikan oleh Tata Usaha';
            }

            $sqlLog = "INSERT INTO document_audit_logs (document_id, user_id, action, notes) VALUES (:doc_id, :user_id, 'TRIAGE_ASSIGNED', :notes)";

            $this->db->query($sqlLog);
            $this->db->bind('doc_id', $id);
            $this->db->bind('user_id', $adminId);
            $this->db->bind('notes', $auditNotes);
            $this->db->execute();

            $this->db->query("COMMIT");
            $this->db->execute();

            $success = true;
        } catch (Exception $e) {
            $this->db->query("ROLLBACK");
            $this->db->execute();

            $success = false;
        }

        return $success;
    }

    public function rejectTriageDisposition($id, $adminId, $rejectionReason)
    {
        try {
            $this->db->query("BEGIN");
            $this->db->execute();

            $sqlUpdate = "UPDATE " . $this->table . " SET status = 'REJECTED', triage_handled_by = :admin_id, rejection_reason = :rejection_reason, updated_at = CURRENT_TIMESTAMP WHERE id = :id AND status = 'PENDING_TRIAGE'";

            $this->db->query($sqlUpdate);
            $this->db->bind('id', $id);
            $this->db->bind('admin_id', $adminId);
            $this->db->bind('rejection_reason', $rejectionReason);
            $this->db->execute();

            if ($this->db->rowCount() === 0) {
                throw new Exception('Dokumen tidak berada pada antrean disposisi.');
            }

            $this->db->query("DELETE FROM document_signers WHERE document_id = :doc_id AND status = 'WAITING'");
            $this->db->bind('doc_id', $id);
            $this->db->execute();

            $sqlLog = "INSERT INTO document_audit_logs (document_id, user_id, action, notes) VALUES (:doc_id, :user_id, 'REJECTED', :notes)";

            $this->db->query($sqlLog);
            $this->db->bind('doc_id', $id);
            $this->db->bind('user_id', $adminId);
            $this->db->bind('notes', 'Dokumen ditolak pada meja disposisi Tata Usaha. Alasan: ' . $rejectionReason);
            $this->db->execute();

            $this->db->query("COMMIT");
            $this->db->execute();

            $success = true;
        } catch (Exception $e) {
            $this->db->query("ROLLBACK");
            $this->db->execute();

            $success = false;
        }

        return $success;
    }

    public function getAllDocumentsWithDetails($search = '', $status = '', $limit = 50, $userId = null)
    {
        if (!empty($userId)) {
            $sql = "SELECT d.id, d.requester_id, d.document_number, d.title, d.status, d.routing_type, d.created_at, d.completed_at, u.full_name AS requester_name, u.identity_number AS requester_nim, ds.status AS signer_status, ds.signed_at FROM " . $this->table . " d JOIN users u ON u.id = d.requester_id LEFT JOIN document_signers ds ON ds.document_id = d.id AND ds.signer_id = :signer_id WHERE (d.requester_id = :requester_id OR (ds.signer_id IS NOT NULL AND ds.status != 'WAITING'))";
        } else {
            $sql = "SELECT d.id, d.requester_id, d.document_number, d.title, d.status, d.routing_type, d.created_at, d.completed_at, u.full_name AS requester_name, u.identity_number AS requester_nim, NULL AS signer_status, NULL AS signed_at FROM " . $this->table . " d JOIN users u ON u.id = d.requester_id WHERE d.status <> 'DRAFT'";
        }

        if (!empty($search)) {
            $sql .= " AND (d.title ILIKE :search OR d.document_number ILIKE :search OR u.full_name ILIKE :search)";
        }

        if (!empty($status)) {
            $sql .= " AND d.status = :status";
        }

        if (!empty($userId)) {
            $orderBy = "COALESCE(d.completed_at, ds.signed_at, d.created_at) DESC";
        } else {
            $orderBy = "COALESCE(d.completed_at, d.created_at) DESC";
        }

        $sql .= " ORDER BY " . $orderBy . " LIMIT :limit";

        $this->db->query($sql);

        if (!empty($userId)) {
            $this->db->bind('signer_id', $userId);
            $this->db->bind('requester_id', $userId);
        }

        if (!empty($search)) {
            $this->db->bind('search', '%' . $search . '%');
        }

        if (!empty($status)) {
            $this->db->bind('status', $status);
        }

        $this->db->bind('limit', (int) $limit);

        return $this->db->resultSet();
    }

    public function getArchivedDocuments($search = '', $status = 'all')
    {
        $sql = "SELECT d.id, d.document_number, d.title, d.status, d.created_at, d.completed_at, d.updated_at, d.rejection_reason, u.full_name AS requester_name, u.identity_number AS requester_nim FROM " . $this->table . " d JOIN users u ON u.id = d.requester_id WHERE d.status IN ('COMPLETED', 'REJECTED', 'CANCELLED')";

        if ($status !== 'all') {
            $sql .= " AND d.status = :status";
        }

        if (!empty($search)) {
            $sql .= " AND (d.title ILIKE :search OR d.document_number ILIKE :search OR u.full_name ILIKE :search OR u.identity_number ILIKE :search)";
        }

        $sql .= " ORDER BY COALESCE(d.completed_at, d.updated_at) DESC";

        $this->db->query($sql);

        if ($status !== 'all') {
            $this->db->bind('status', $status);
        }

        if (!empty($search)) {
            $this->db->bind('search', '%' . $search . '%');
        }

        return $this->db->resultSet();
    }

    public function getArchiveStatistics()
    {
        $sql = "SELECT COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) AS completed_count, COUNT(CASE WHEN status = 'REJECTED' THEN 1 END) AS rejected_count, COUNT(CASE WHEN status = 'CANCELLED' THEN 1 END) AS cancelled_count, COUNT(*) AS total_archived FROM " . $this->table . " WHERE status IN ('COMPLETED', 'REJECTED', 'CANCELLED')";

        $this->db->query($sql);

        return $this->db->single();
    }

    public function isDocumentNumberTaken($documentNumber, $excludeId = null)
    {
        $sql = "SELECT id FROM " . $this->table . " WHERE document_number = :document_number";

        if (!empty($excludeId)) {
            $sql .= " AND id <> :exclude_id";
        }

        $sql .= " LIMIT 1";

        $this->db->query($sql);
        $this->db->bind('document_number', $documentNumber);

        if (!empty($excludeId)) {
            $this->db->bind('exclude_id', $excludeId);
        }

        return $this->db->single();
    }

    public function getNextDocumentSequence()
    {
        $sql = "SELECT document_number FROM " . $this->table . " WHERE document_number IS NOT NULL";

        $this->db->query($sql);
        $rows = $this->db->resultSet();
        $maxSeq = 0;

        foreach ($rows as $row) {
            $num = (string) ($row['document_number'] ?? '');

            if (preg_match('/\A(\d+)/', $num, $matches)) {
                $val = (int) $matches[1];

                if ($val > $maxSeq) {
                    $maxSeq = $val;
                }
            }
        }

        return str_pad((string) ($maxSeq + 1), 3, '0', STR_PAD_LEFT);
    }

    public function getDashboardStatistics()
    {
        $sql = "SELECT COUNT(CASE WHEN status <> 'DRAFT' THEN 1 END) AS total_documents, COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) AS total_completed, COUNT(CASE WHEN status = 'IN_PROGRESS' THEN 1 END) AS total_in_progress, COUNT(CASE WHEN status = 'PENDING_TRIAGE' THEN 1 END) AS total_pending_triage FROM " . $this->table;

        $this->db->query($sql);

        return $this->db->single();
    }

    public function getAllUserDocuments($userId)
    {
        $sql = "SELECT d.id, d.title, d.document_number, d.status, d.created_at, d.original_file_path, d.final_file_path,
                (SELECT COUNT(*) FROM document_signers s WHERE s.document_id = d.id) AS total_signers,
                (SELECT COUNT(*) FROM document_signers s WHERE s.document_id = d.id AND s.status = 'SIGNED') AS signed_count
                FROM " . $this->table . " d
                WHERE d.requester_id = :user_id
                ORDER BY d.created_at DESC";

        $this->db->query($sql);
        $this->db->bind('user_id', $userId);

        return $this->db->resultSet();
    }

    public function getDocumentByIdAndUser($id, $userId)
    {
        $document = false;

        if (isValidUuid($id) && isValidUuid($userId)) {
            $sql = "SELECT d.*,
                           req.full_name AS requester_name,
                           req.identity_number AS requester_nim,
                           req.identity_number AS requester_nim_nip,
                           req.email AS requester_email,
                           req.position AS requester_position,
                           req.study_program AS requester_study_program,
                           requester_role.name AS requester_role_name,
                           requester_role.display_name AS requester_role_display_name
                    FROM " . $this->table . " d
                    JOIN users req ON req.id = d.requester_id
                    LEFT JOIN roles requester_role ON requester_role.id = req.role_id
                    WHERE d.id = :id
                      AND d.requester_id = :user_id
                    LIMIT 1";

            $this->db->query($sql);
            $this->db->bind('id', $id);
            $this->db->bind('user_id', $userId);

            $document = $this->db->single();
        }

        return $document;
    }

    public function getSignersByDocumentId($documentId)
    {
        $signers = [];

        if (isValidUuid($documentId)) {
            $sql = "SELECT u.full_name, u.email, u.position, u.study_program, r.display_name AS role_display_name, s.signer_id, s.status, s.signed_at, s.rejection_reason, s.page_number
                    FROM document_signers s
                    JOIN users u ON u.id = s.signer_id
                    LEFT JOIN roles r ON r.id = u.role_id
                    WHERE s.document_id = :document_id
                    ORDER BY s.signing_order ASC";

            $this->db->query($sql);
            $this->db->bind('document_id', $documentId);

            $signers = $this->db->resultSet();
        }

        return $signers;
    }

    public function verifyDocument($search)
    {
        $search = trim((string) $search);
        $result = false;

        if (!empty($search)) {
            $isUuid = isValidUuid($search);

            $sql = "SELECT d.*, u.full_name AS requester_name, u.identity_number AS requester_nim, u.email AS requester_email, u.position AS requester_position, u.study_program AS requester_study_program, r.display_name AS requester_role_display_name
                    FROM " . $this->table . " d
                    JOIN users u ON u.id = d.requester_id
                    LEFT JOIN roles r ON r.id = u.role_id
                    WHERE d.status = 'COMPLETED'
                      AND d.final_sha256_hash IS NOT NULL
                      AND d.final_sha256_hash <> ''
                      AND (d.final_sha256_hash = :search";

            if ($isUuid) {
                $sql .= " OR d.id = :search_uuid";
            }

            $sql .= ") LIMIT 1";

            $this->db->query($sql);
            $this->db->bind('search', $search);

            if ($isUuid) {
                $this->db->bind('search_uuid', $search);
            }

            $document = $this->db->single();

            if ($document) {
                $signersSql = "SELECT s.*, u.full_name, u.identity_number, u.position, u.study_program, r.display_name AS role_display_name, c.certificate_serial, c.public_key, c.status AS cert_status, c.valid_from, c.valid_until, c.revoked_at
                               FROM document_signers s
                               JOIN users u ON u.id = s.signer_id
                               LEFT JOIN roles r ON r.id = u.role_id
                               LEFT JOIN signer_certificates c ON c.id = s.certificate_id
                               WHERE s.document_id = :doc_id
                               ORDER BY s.signing_order ASC";

                $this->db->query($signersSql);
                $this->db->bind('doc_id', $document['id']);

                $document['signers'] = $this->db->resultSet();
                $result = $document;
            }
        }

        return $result;
    }

    public function createDocument($data, $signerIds = [])
    {
        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO " . $this->table . " (document_number, title, requester_id, original_file_path, initial_sha256_hash, status, routing_type, is_sequential, expires_at) VALUES (:document_number, :title, :requester_id, :original_file_path, :initial_sha256_hash, :status, :routing_type, :is_sequential, :expires_at) RETURNING id";

            $this->db->query($sql);
            $this->db->bind('document_number', !empty($data['document_number']) ? $data['document_number'] : null);
            $this->db->bind('title', $data['title']);
            $this->db->bind('requester_id', $data['requester_id']);
            $this->db->bind('original_file_path', $data['original_file_path']);
            $this->db->bind('initial_sha256_hash', $data['initial_sha256_hash']);
            $this->db->bind('status', $data['status']);
            $this->db->bind('routing_type', $data['routing_type']);
            $this->db->bind('is_sequential', !empty($data['is_sequential']) ? 'true' : 'false');
            $this->db->bind('expires_at', !empty($data['expires_at']) ? $data['expires_at'] : null);

            $result = $this->db->single();

            if (!$result || empty($result['id'])) {
                throw new Exception('Gagal membuat dokumen baru.');
            }

            $docId = $result['id'];

            if (!empty($signerIds)) {
                $order = 1;

                foreach ($signerIds as $signerId) {
                    $signerSql = "INSERT INTO document_signers (document_id, signer_id, signing_order) VALUES (:doc_id, :signer_id, :signing_order)";

                    $this->db->query($signerSql);
                    $this->db->bind('doc_id', $docId);
                    $this->db->bind('signer_id', $signerId);
                    $this->db->bind('signing_order', $order);
                    $this->db->execute();

                    $order++;
                }
            }

            $auditSql = "INSERT INTO document_audit_logs (document_id, user_id, action, notes) VALUES (:doc_id, :user_id, 'CREATED', 'Dokumen diajukan')";

            $this->db->query($auditSql);
            $this->db->bind('doc_id', $docId);
            $this->db->bind('user_id', $data['requester_id']);
            $this->db->execute();

            $this->db->commit();

            return $docId;
        } catch (Exception $e) {
            $this->db->rollBack();

            return false;
        }
    }

    public function getSignersForPositioning($documentId)
    {
        $signers = [];

        if (isValidUuid($documentId)) {
            $sql = "SELECT ds.signer_id AS id, ds.signing_order, ds.stamp_type, u.full_name AS name, u.position, u.study_program, r.display_name AS role_display_name
                    FROM document_signers ds
                    JOIN users u ON u.id = ds.signer_id
                    LEFT JOIN roles r ON r.id = u.role_id
                    WHERE ds.document_id = :doc_id
                    ORDER BY ds.signing_order ASC";

            $this->db->query($sql);
            $this->db->bind('doc_id', $documentId);

            $signers = $this->db->resultSet();
        }

        return $signers;
    }

    public function deleteDraft($id, $userId)
    {
        $deletedFilePath = false;

        if (isValidUuid($id) && isValidUuid($userId)) {
            try {
                $this->db->beginTransaction();

                $this->db->query("SELECT original_file_path FROM " . $this->table . " WHERE id = :id AND requester_id = :user_id AND status = 'DRAFT' FOR UPDATE");
                $this->db->bind('id', $id);
                $this->db->bind('user_id', $userId);
                $doc = $this->db->single();

                if ($doc) {
                    $this->db->query("DELETE FROM " . $this->table . " WHERE id = :id AND requester_id = :user_id AND status = 'DRAFT'");
                    $this->db->bind('id', $id);
                    $this->db->bind('user_id', $userId);
                    $this->db->execute();

                    if ($this->db->rowCount() === 1) {
                        $deletedFilePath = $doc['original_file_path'] ?? '';
                    }
                }

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();

                $deletedFilePath = false;
            }
        }

        return $deletedFilePath;
    }

    public function cancelDocument($id, $userId, $reason, $isAdmin = false)
    {
        $result = ['ok' => false, 'message' => 'Permintaan pembatalan dokumen tidak valid.'];
        $cleanReason = is_string($reason) ? trim($reason) : '';

        if (!isValidUuid($id) || !isValidUuid($userId)) {
            $result = ['ok' => false, 'message' => 'Identitas dokumen atau pengguna tidak valid.'];
        } elseif ($cleanReason === '' || mb_strlen($cleanReason) > 1000) {
            $result = ['ok' => false, 'message' => 'Alasan pembatalan wajib diisi dan maksimal 1000 karakter.'];
        } else {
            $pdo = $this->db->getConnection();
            $partialSignedPath = null;

            try {
                $pdo->beginTransaction();

                $docQuery = $pdo->prepare(
                    "SELECT id, requester_id, status, final_file_path
                     FROM " . $this->table . "
                     WHERE id = ?
                     FOR UPDATE"
                );
                $docQuery->execute([$id]);
                $doc = $docQuery->fetch(PDO::FETCH_ASSOC);

                if (!$doc) {
                    throw new DomainException('Dokumen tidak ditemukan.');
                }

                if (!$isAdmin && (string) ($doc['requester_id'] ?? '') !== (string) $userId) {
                    throw new DomainException('Anda tidak memiliki izin membatalkan dokumen ini.');
                }

                $docStatus = (string) ($doc['status'] ?? '');

                if (!in_array($docStatus, ['PENDING_TRIAGE', 'IN_PROGRESS'], true)) {
                    throw new DomainException('Dokumen pada status saat ini tidak dapat dibatalkan.');
                }

                $signerQuery = $pdo->prepare(
                    "SELECT id, status
                     FROM document_signers
                     WHERE document_id = ?
                     FOR UPDATE"
                );
                $signerQuery->execute([$id]);
                $signers = $signerQuery->fetchAll(PDO::FETCH_ASSOC);
                $signedCount = 0;

                foreach ($signers as $signerRow) {
                    if (($signerRow['status'] ?? '') === 'SIGNED') {
                        $signedCount++;
                    }
                }

                if (!$isAdmin && $signedCount > 0) {
                    throw new DomainException('Dokumen tidak dapat dibatalkan karena sudah ada penandatangan yang membubuhkan tanda tangan.');
                }

                $actorQuery = $pdo->prepare(
                    "SELECT status
                     FROM users
                     WHERE id = ?
                     FOR UPDATE"
                );
                $actorQuery->execute([$userId]);
                $actor = $actorQuery->fetch(PDO::FETCH_ASSOC);

                if (!$actor || ($actor['status'] ?? '') !== 'ACTIVE') {
                    throw new DomainException('Akun Anda tidak aktif atau ditangguhkan.');
                }

                $finalFilePath = $doc['final_file_path'] ?? '';

                if (is_string($finalFilePath) && preg_match('/\Auploads\/signed\/[A-Za-z0-9._-]+\.pdf\z/i', $finalFilePath)) {
                    $partialSignedPath = dirname(__DIR__) . DIRECTORY_SEPARATOR
                        . str_replace('/', DIRECTORY_SEPARATOR, $finalFilePath);
                }

                $pdo->prepare("UPDATE document_signers SET status = 'CANCELLED' WHERE document_id = ? AND status IN ('WAITING', 'CURRENT_TURN')")
                    ->execute([$id]);

                $updateDoc = $pdo->prepare(
                    "UPDATE " . $this->table . "
                     SET status = 'CANCELLED',
                         rejection_reason = ?,
                         final_file_path = NULL,
                         final_sha256_hash = NULL,
                         updated_at = CURRENT_TIMESTAMP
                     WHERE id = ? AND status IN ('PENDING_TRIAGE', 'IN_PROGRESS')"
                );
                $updateDoc->execute([$cleanReason, $id]);

                if ($updateDoc->rowCount() !== 1) {
                    throw new DomainException('Status dokumen berubah saat pembatalan diproses.');
                }

                $auditNotes = ($isAdmin
                    ? 'Pengajuan dokumen dibatalkan oleh Administrator Tata Usaha. Alasan: '
                    : 'Pengajuan dokumen dibatalkan oleh pemohon. Alasan: ') . $cleanReason;

                $pdo->prepare(
                    "INSERT INTO document_audit_logs (document_id, user_id, action, notes)
                     VALUES (?, ?, 'CANCELLED', ?)"
                )->execute([$id, $userId, $auditNotes]);

                $pdo->commit();

                if ($partialSignedPath !== null && is_file($partialSignedPath)) {
                    @unlink($partialSignedPath);
                }

                $result = ['ok' => true, 'message' => 'Pengajuan dibatalkan.'];
            } catch (DomainException $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $result = ['ok' => false, 'message' => $exception->getMessage()];
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                error_log('Document::cancelDocument failed for ' . $id . ': ' . $exception->getMessage());

                $result = ['ok' => false, 'message' => 'Gagal membatalkan pengajuan.'];
            }
        }

        return $result;
    }

    public function updateSignerPositions($documentId, $positions, $userId, $expectedStatus)
    {
        $result = ['ok' => false, 'message' => 'Gagal menyimpan posisi TTE.'];

        if (
            !isValidUuid($documentId)
            || !isValidUuid($userId)
            || !in_array($expectedStatus, ['DRAFT', 'PENDING_TRIAGE'], true)
            || !is_array($positions)
            || count($positions) === 0
        ) {
            $result = ['ok' => false, 'message' => 'Data posisi tanda tangan tidak valid.'];
        } else {
            $pdo = $this->db->getConnection();

            try {
                $pdo->beginTransaction();

                $docQuery = $pdo->prepare(
                    "SELECT id, original_file_path, status, is_sequential
                     FROM " . $this->table . "
                     WHERE id = ?
                     FOR UPDATE"
                );
                $docQuery->execute([$documentId]);
                $doc = $docQuery->fetch(PDO::FETCH_ASSOC);

                if (!$doc || ($doc['status'] ?? '') !== $expectedStatus) {
                    throw new DomainException('Status dokumen sudah berubah dan tidak dapat diatur posisinya.');
                }

                $isSequential = in_array($doc['is_sequential'], [true, 1, '1', 't', 'true'], true);
                $relativePdfPath = ltrim((string) ($doc['original_file_path'] ?? ''), '/');

                if (!preg_match('/\Auploads\/(?:documents|signed)\/[A-Za-z0-9._-]+\.pdf\z/i', $relativePdfPath)) {
                    throw new DomainException('Berkas PDF dokumen tidak valid.');
                }

                $pageSizes = PDFStamper::getPageSizes($relativePdfPath);
                $pageCount = count($pageSizes);

                if ($pageCount < 1) {
                    throw new DomainException('Berkas PDF dokumen tidak ditemukan atau tidak dapat dibaca.');
                }

                $expectedSignersQuery = $pdo->prepare(
                    "SELECT signer_id
                     FROM document_signers
                     WHERE document_id = ?
                     FOR UPDATE"
                );
                $expectedSignersQuery->execute([$documentId]);
                $expectedSignerIds = $expectedSignersQuery->fetchAll(PDO::FETCH_COLUMN);

                if (count($expectedSignerIds) === 0) {
                    throw new DomainException('Daftar penandatangan dokumen tidak ditemukan.');
                }

                if (count($positions) !== count($expectedSignerIds)) {
                    throw new DomainException('Tempatkan semua kotak TTE.');
                }

                $expectedLookup = array_flip($expectedSignerIds);
                $slotDimensions = PDFStamper::getReferenceSlotDimensionsMm();
                $validatedPositions = [];
                $boxesByPage = [];

                foreach ($positions as $position) {
                    if (!is_array($position)) {
                        throw new DomainException('Format koordinat posisi tanda tangan tidak valid.');
                    }

                    $signerId = trim((string) ($position['signer_id'] ?? ''));

                    if (!isValidUuid($signerId) || !isset($expectedLookup[$signerId]) || isset($validatedPositions[$signerId])) {
                        throw new DomainException('Data penandatangan pada posisi tanda tangan tidak valid.');
                    }

                    $rawPage = $position['page'] ?? null;
                    $isPageInt = is_int($rawPage) || (is_string($rawPage) && preg_match('/\A[1-9]\d*\z/', $rawPage) === 1);
                    $page = $isPageInt ? (int) $rawPage : 0;

                    if ($page < 1 || $page > $pageCount || !isset($pageSizes[$page])) {
                        throw new DomainException('Nomor halaman posisi tanda tangan melebihi jumlah halaman PDF.');
                    }

                    $rawX = $position['x'] ?? null;
                    $rawY = $position['y'] ?? null;

                    if (!is_numeric($rawX) || !is_numeric($rawY)) {
                        throw new DomainException('Koordinat posisi tanda tangan tidak valid.');
                    }

                    $coordX = (float) $rawX;
                    $coordY = (float) $rawY;

                    if (
                        !is_finite($coordX)
                        || !is_finite($coordY)
                        || $coordX < 0.0
                        || $coordX > 1.0
                        || $coordY < 0.0
                        || $coordY > 1.0
                    ) {
                        throw new DomainException('Koordinat posisi tanda tangan berada di luar batas halaman.');
                    }

                    $pageWidth = (float) $pageSizes[$page]['width'];
                    $pageHeight = (float) $pageSizes[$page]['height'];
                    $slotWidth = (float) $slotDimensions['width'];
                    $slotHeight = (float) $slotDimensions['height'];

                    if ($pageWidth < $slotWidth || $pageHeight < $slotHeight) {
                        throw new DomainException('Halaman PDF terlalu kecil untuk kotak TTE.');
                    }

                    $boxX = max(0.0, min($coordX * $pageWidth, $pageWidth - $slotWidth));
                    $boxY = max(0.0, min($coordY * $pageHeight, $pageHeight - $slotHeight));

                    $validatedPositions[$signerId] = [
                        'signer_id' => $signerId,
                        'page' => $page,
                        'x' => $coordX,
                        'y' => $coordY,
                    ];
                    $boxesByPage[$page][] = [
                        'left' => $boxX,
                        'top' => $boxY,
                        'right' => $boxX + $slotWidth,
                        'bottom' => $boxY + $slotHeight,
                    ];
                }

                if (count($validatedPositions) !== count($expectedSignerIds)) {
                    throw new DomainException('Tempatkan semua kotak TTE.');
                }

                foreach ($boxesByPage as $pageBoxes) {
                    $boxTotal = count($pageBoxes);

                    for ($i = 0; $i < $boxTotal; $i++) {
                        for ($j = $i + 1; $j < $boxTotal; $j++) {
                            $overlapWidth = min($pageBoxes[$i]['right'], $pageBoxes[$j]['right']) - max($pageBoxes[$i]['left'], $pageBoxes[$j]['left']);
                            $overlapHeight = min($pageBoxes[$i]['bottom'], $pageBoxes[$j]['bottom']) - max($pageBoxes[$i]['top'], $pageBoxes[$j]['top']);

                            if ($overlapWidth > 1.0 && $overlapHeight > 1.0) {
                                throw new DomainException('Kotak TTE bertumpuk. Geser salah satunya.');
                            }
                        }
                    }
                }

                $updateDoc = $pdo->prepare(
                    "UPDATE " . $this->table . "
                     SET status = 'IN_PROGRESS',
                         updated_at = CURRENT_TIMESTAMP
                     WHERE id = ? AND status = ?"
                );
                $updateDoc->execute([$documentId, $expectedStatus]);

                if ($updateDoc->rowCount() !== 1) {
                    throw new DomainException('Status dokumen sudah berubah.');
                }

                $updatePos = $pdo->prepare(
                    "UPDATE document_signers
                     SET page_number = ?, coord_x = ?, coord_y = ?
                     WHERE document_id = ? AND signer_id = ?"
                );

                foreach ($validatedPositions as $p) {
                    $updatePos->execute([$p['page'], $p['x'], $p['y'], $documentId, $p['signer_id']]);

                    if ($updatePos->rowCount() !== 1) {
                        throw new DomainException('Gagal memperbarui posisi salah satu penandatangan.');
                    }
                }

                if ($isSequential) {
                    $turnQuery = $pdo->prepare(
                        "UPDATE document_signers
                         SET status = 'CURRENT_TURN'
                         WHERE document_id = ? AND signing_order = 1"
                    );
                } else {
                    $turnQuery = $pdo->prepare(
                        "UPDATE document_signers
                         SET status = 'CURRENT_TURN'
                         WHERE document_id = ?"
                    );
                }

                $turnQuery->execute([$documentId]);

                if ($turnQuery->rowCount() < 1) {
                    throw new DomainException('Gagal mengaktifkan giliran penandatangan.');
                }

                $pdo->prepare(
                    "INSERT INTO document_audit_logs (document_id, user_id, action, notes)
                     VALUES (?, ?, 'POSITIONS_SET', 'Posisi TTE ditetapkan, dokumen dikirim')"
                )->execute([$documentId, $userId]);

                $pdo->commit();

                $result = ['ok' => true, 'message' => ''];
            } catch (DomainException $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $result = ['ok' => false, 'message' => $exception->getMessage()];
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                error_log('Document::updateSignerPositions failed for ' . $documentId . ': ' . $exception->getMessage());

                $result = ['ok' => false, 'message' => 'Gagal menyimpan posisi TTE.'];
            }
        }

        return $result;
    }
}
