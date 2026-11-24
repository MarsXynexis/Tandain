<?php

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
        $sql = "SELECT d.id, d.document_number, d.title, d.status, d.routing_type, d.original_file_path, d.created_at, u.full_name AS requester_name, u.identity_number AS requester_nim, u.email AS requester_email, u.position AS requester_position, u.study_program AS requester_study_program, requester_role.display_name AS requester_role_display_name FROM " . $this->table . " d JOIN users u ON u.id = d.requester_id LEFT JOIN roles requester_role ON requester_role.id = u.role_id WHERE ";

        if ($status === 'dispatched') {
            $sql .= "(d.status = 'IN_PROGRESS' AND d.triage_handled_by IS NOT NULL)";
        } else if ($status === 'rejected') {
            $sql .= "(d.status = 'REJECTED' AND d.triage_handled_by IS NOT NULL)";
        } else if ($status === 'all') {
            $sql .= "(d.status = 'PENDING_TRIAGE' OR (d.status IN ('IN_PROGRESS', 'REJECTED') AND d.triage_handled_by IS NOT NULL))";
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
        $sql = "SELECT COUNT(CASE WHEN status = 'PENDING_TRIAGE' THEN 1 END) AS pending_count, COUNT(CASE WHEN status = 'IN_PROGRESS' AND triage_handled_by IS NOT NULL THEN 1 END) AS dispatched_count, COUNT(CASE WHEN status = 'REJECTED' AND triage_handled_by IS NOT NULL THEN 1 END) AS rejected_count, COUNT(CASE WHEN status = 'PENDING_TRIAGE' OR (status IN ('IN_PROGRESS', 'REJECTED') AND triage_handled_by IS NOT NULL) THEN 1 END) AS total_triage FROM " . $this->table;

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
            $sql = "SELECT ds.signer_id AS id, ds.signing_order, ds.stamp_type, u.full_name AS name FROM document_signers ds JOIN users u ON u.id = ds.signer_id WHERE ds.document_id = :doc_id ORDER BY ds.signing_order ASC";

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

    public function updateSignerPositions($documentId, $positions, $isSequential, $userId, $expectedStatus)
    {
        $saved = false;

        try {
            $this->db->beginTransaction();

            $docStatusSql = "UPDATE " . $this->table . " SET status = 'IN_PROGRESS', updated_at = CURRENT_TIMESTAMP WHERE id = :doc_id AND status = :expected_status";

            $this->db->query($docStatusSql);
            $this->db->bind('doc_id', $documentId);
            $this->db->bind('expected_status', $expectedStatus);
            $this->db->execute();

            if ($this->db->rowCount() !== 1) {
                throw new RuntimeException('Status dokumen sudah berubah.');
            }

            $updatePosSql = "UPDATE document_signers SET page_number = :page, coord_x = :coord_x, coord_y = :coord_y WHERE document_id = :doc_id AND signer_id = :signer_id";

            foreach ($positions as $p) {
                $page = max(1, (int)($p['page'] ?? 1));
                $coordX = max(0, min(1, (float)($p['x'] ?? 0)));
                $coordY = max(0, min(1, (float)($p['y'] ?? 0)));

                $this->db->query($updatePosSql);
                $this->db->bind('page', $page);
                $this->db->bind('coord_x', $coordX);
                $this->db->bind('coord_y', $coordY);
                $this->db->bind('doc_id', $documentId);
                $this->db->bind('signer_id', $p['signer_id']);
                $this->db->execute();
            }

            if ($isSequential) {
                $turnSql = "UPDATE document_signers SET status = 'CURRENT_TURN' WHERE document_id = :doc_id AND signing_order = 1";
            } else {
                $turnSql = "UPDATE document_signers SET status = 'CURRENT_TURN' WHERE document_id = :doc_id";
            }

            $this->db->query($turnSql);
            $this->db->bind('doc_id', $documentId);
            $this->db->execute();

            $auditSql = "INSERT INTO document_audit_logs (document_id, user_id, action, notes) VALUES (:doc_id, :user_id, 'POSITIONS_SET', 'Posisi TTE ditetapkan, dokumen dikirim')";

            $this->db->query($auditSql);
            $this->db->bind('doc_id', $documentId);
            $this->db->bind('user_id', $userId);
            $this->db->execute();

            $this->db->commit();

            $saved = true;
        } catch (Exception $e) {
            $this->db->rollBack();
        }

        return $saved;
    }
}
