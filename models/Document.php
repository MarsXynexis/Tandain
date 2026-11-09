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
        $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1");

        $this->db->bind('id', $id);

        return $this->db->single();
    }

    public function findByIdWithRequester($id)
    {
        $sql = "SELECT d.*, u.full_name AS requester_name, u.identity_number AS requester_nim, u.email AS requester_email, u.position AS requester_position, t.full_name AS triage_admin_name FROM " . $this->table . " d JOIN users u ON u.id = d.requester_id LEFT JOIN users t ON t.id = d.triage_handled_by WHERE d.id = :id LIMIT 1";

        $this->db->query($sql);

        $this->db->bind('id', $id);

        return $this->db->single();
    }

    public function getPendingTriageDocuments($status = 'pending', $search = '')
    {
        $sql = "SELECT d.id, d.document_number, d.title, d.status, d.routing_type, d.original_file_path, d.created_at, u.full_name AS requester_name, u.identity_number AS requester_nim, u.email AS requester_email, u.position AS requester_position FROM " . $this->table . " d JOIN users u ON u.id = d.requester_id WHERE ";

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

            $sqlUpdate = "UPDATE " . $this->table . " SET status = 'IN_PROGRESS', triage_handled_by = :admin_id, document_number = :document_number, is_sequential = :is_sequential, issued_at = CURRENT_TIMESTAMP, expires_at = :expires_at, updated_at = CURRENT_TIMESTAMP WHERE id = :id AND status = 'PENDING_TRIAGE'";

            $this->db->query($sqlUpdate);
            $this->db->bind('id', $id);
            $this->db->bind('admin_id', $adminId);
            $this->db->bind('document_number', $documentNumber);
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
                if ($isSequential) {
                    $status = ($order === 1) ? 'CURRENT_TURN' : 'WAITING';
                } else {
                    $status = 'CURRENT_TURN';
                }

                $stampType = $signer['stamp_type'] ?? 'DUAL_DISPLAY';
                $pageNumber = !empty($signer['page_number']) ? (int)$signer['page_number'] : 1;
                $coordX = !empty($signer['coord_x']) ? (float)$signer['coord_x'] : 120;
                $coordY = !empty($signer['coord_y']) ? (float)$signer['coord_y'] : 650;

                $sqlInsertSigner = "INSERT INTO document_signers (document_id, signer_id, signing_order, status, stamp_type, page_number, coord_x, coord_y) VALUES (:doc_id, :signer_id, :signing_order, :status, :stamp_type, :page_number, :coord_x, :coord_y)";

                $this->db->query($sqlInsertSigner);
                $this->db->bind('doc_id', $id);
                $this->db->bind('signer_id', $signer['signer_id']);
                $this->db->bind('signing_order', $order);
                $this->db->bind('status', $status);
                $this->db->bind('stamp_type', $stampType);
                $this->db->bind('page_number', $pageNumber);
                $this->db->bind('coord_x', $coordX);
                $this->db->bind('coord_y', $coordY);
                $this->db->execute();

                $order++;
            }

            $auditNotes = 'Dokumen didisposisikan oleh Tata Usaha dengan nomor agenda ' . $documentNumber;

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

    public function getAllDocumentsWithDetails($search = '', $status = '', $limit = 50, $requesterId = null)
    {
        $sql = "SELECT d.id, d.document_number, d.title, d.status, d.routing_type, d.created_at, d.completed_at, u.full_name AS requester_name, u.identity_number AS requester_nim FROM " . $this->table . " d JOIN users u ON u.id = d.requester_id WHERE 1=1";

        if (!empty($search)) {
            $sql .= " AND (d.title ILIKE :search OR d.document_number ILIKE :search OR u.full_name ILIKE :search)";
        }

        if (!empty($status)) {
            $sql .= " AND d.status = :status";
        }

        if (!empty($requesterId)) {
            $sql .= " AND d.requester_id = :requester_id";
        }

        $sql .= " ORDER BY d.created_at DESC LIMIT :limit";

        $this->db->query($sql);

        if (!empty($search)) {
            $this->db->bind('search', '%' . $search . '%');
        }

        if (!empty($status)) {
            $this->db->bind('status', $status);
        }

        if (!empty($requesterId)) {
            $this->db->bind('requester_id', $requesterId);
        }

        $this->db->bind('limit', $limit);

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

    public function getDashboardStatistics()
    {
        $sql = "SELECT COUNT(*) AS total_documents, COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) AS total_completed, COUNT(CASE WHEN status = 'IN_PROGRESS' THEN 1 END) AS total_in_progress, COUNT(CASE WHEN status = 'PENDING_TRIAGE' THEN 1 END) AS total_pending_triage FROM " . $this->table;

        $this->db->query($sql);

        return $this->db->single();
    }

    public function countUserDocuments($userId, $search = '', $filter = 'all')
    {
        $sql = "SELECT COUNT(*) AS total FROM " . $this->table . " d WHERE d.requester_id = :user_id";

        if (!empty($search)) {
            $sql .= " AND (d.title ILIKE :search OR d.document_number ILIKE :search)";
        }

        if ($filter === 'completed') {
            $sql .= " AND d.status = 'COMPLETED'";
        } else if ($filter === 'in_progress') {
            $sql .= " AND d.status IN ('PENDING_TRIAGE', 'IN_PROGRESS')";
        }

        $this->db->query($sql);
        $this->db->bind('user_id', $userId);

        if (!empty($search)) {
            $this->db->bind('search', '%' . $search . '%');
        }

        $row = $this->db->single();

        return (int) ($row['total'] ?? 0);
    }

    public function getUserDocuments($userId, $search = '', $filter = 'all', $limit = 8, $offset = 0)
    {
        $sql = "SELECT d.id, d.title, d.document_number, d.status, d.created_at, d.original_file_path, d.final_file_path, 
                (SELECT COUNT(*) FROM document_signers s WHERE s.document_id = d.id) AS total_signers, 
                (SELECT COUNT(*) FROM document_signers s WHERE s.document_id = d.id AND s.status = 'SIGNED') AS signed_count 
                FROM " . $this->table . " d WHERE d.requester_id = :user_id";

        if (!empty($search)) {
            $sql .= " AND (d.title ILIKE :search OR d.document_number ILIKE :search)";
        }

        if ($filter === 'completed') {
            $sql .= " AND d.status = 'COMPLETED'";
        } else if ($filter === 'in_progress') {
            $sql .= " AND d.status IN ('PENDING_TRIAGE', 'IN_PROGRESS')";
        }

        $sql .= " ORDER BY d.created_at DESC LIMIT :limit OFFSET :offset";

        $this->db->query($sql);
        $this->db->bind('user_id', $userId);

        if (!empty($search)) {
            $this->db->bind('search', '%' . $search . '%');
        }

        $this->db->bind('limit', (int) $limit);
        $this->db->bind('offset', (int) $offset);

        return $this->db->resultSet();
    }

    public function getUserStatusCounts($userId)
    {
        $sql = "SELECT COUNT(*) AS total_all, 
                COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) AS total_completed, 
                COUNT(CASE WHEN status IN ('PENDING_TRIAGE', 'IN_PROGRESS') THEN 1 END) AS total_in_progress 
                FROM " . $this->table . " WHERE requester_id = :user_id";

        $this->db->query($sql);
        $this->db->bind('user_id', $userId);

        return $this->db->single();
    }

    public function getDocumentByIdAndUser($id, $userId)
    {
        if (!preg_match('/^[0-9a-f-]{36}$/i', $id)) {
            return false;
        }

        $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id AND requester_id = :user_id LIMIT 1");
        $this->db->bind('id', $id);
        $this->db->bind('user_id', $userId);

        return $this->db->single();
    }

    public function getSignersByDocumentId($documentId)
    {
        $sql = "SELECT u.full_name, u.email, u.position, s.status, s.signed_at, s.rejection_reason 
                FROM document_signers s 
                JOIN users u ON u.id = s.signer_id 
                WHERE s.document_id = :document_id 
                ORDER BY s.signing_order ASC";

        $this->db->query($sql);
        $this->db->bind('document_id', $documentId);

        return $this->db->resultSet();
    }

    public function verifyDocument($search)
    {
        $search = trim($search);

        if (empty($search)) {
            return false;
        }

        $sql = "SELECT d.*, u.full_name AS requester_name, u.identity_number AS requester_nim, u.position AS requester_position 
                FROM " . $this->table . " d 
                JOIN users u ON u.id = d.requester_id 
                WHERE d.document_number = :search 
                   OR d.final_sha256_hash = :search 
                   OR d.initial_sha256_hash = :search";

        if (preg_match('/^[0-9a-f-]{36}$/i', $search)) {
            $sql .= " OR d.id = :search_uuid";
        }

        $sql .= " LIMIT 1";

        $this->db->query($sql);
        $this->db->bind('search', $search);

        if (preg_match('/^[0-9a-f-]{36}$/i', $search)) {
            $this->db->bind('search_uuid', $search);
        }

        $document = $this->db->single();

        if (!$document) {
            return false;
        }

        $signersSql = "SELECT s.*, u.full_name, u.identity_number, u.position, c.certificate_serial, c.status AS cert_status, c.valid_from, c.valid_until 
                       FROM document_signers s 
                       JOIN users u ON u.id = s.signer_id 
                       LEFT JOIN signer_certificates c ON c.id = s.certificate_id 
                       WHERE s.document_id = :doc_id 
                       ORDER BY s.signing_order ASC";

        $this->db->query($signersSql);
        $this->db->bind('doc_id', $document['id']);
        $document['signers'] = $this->db->resultSet();

        return $document;
    }
}
