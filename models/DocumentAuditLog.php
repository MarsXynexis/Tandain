<?php

class DocumentAuditLog
{
    private $table = 'document_audit_logs';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getAllLogsWithDetails($limit = 100)
    {
        $sql = "SELECT l.id, l.action, l.notes, l.created_at, d.id AS document_id, d.title AS document_title, d.document_number, d.status AS document_status, u.full_name AS user_full_name, u.identity_number AS user_identity_number, u.email AS user_email, r.name AS user_role_name, r.display_name AS user_role_display FROM " . $this->table . " l JOIN documents d ON d.id = l.document_id LEFT JOIN users u ON u.id = l.user_id LEFT JOIN roles r ON r.id = u.role_id ORDER BY l.created_at DESC LIMIT :limit";

        $this->db->query($sql);
        $this->db->bind('limit', $limit);

        return $this->db->resultSet();
    }

    public function getFilteredLogs($search = '', $action = '', $limit = 100)
    {
        $sql = "SELECT l.id, l.action, l.notes, l.created_at, d.id AS document_id, d.title AS document_title, d.document_number, d.status AS document_status, u.full_name AS user_full_name, u.identity_number AS user_identity_number, u.email AS user_email, r.name AS user_role_name, r.display_name AS user_role_display FROM " . $this->table . " l JOIN documents d ON d.id = l.document_id LEFT JOIN users u ON u.id = l.user_id LEFT JOIN roles r ON r.id = u.role_id WHERE 1=1";

        if (!empty($search)) {
            $sql .= " AND (d.title ILIKE :search OR d.document_number ILIKE :search OR d.id::text ILIKE :search OR u.full_name ILIKE :search OR u.identity_number ILIKE :search OR l.notes ILIKE :search)";
        }

        if (!empty($action)) {
            $sql .= " AND l.action = :action";
        }

        $sql .= " ORDER BY l.created_at DESC LIMIT :limit";

        $this->db->query($sql);

        if (!empty($search)) {
            $this->db->bind('search', '%' . $search . '%');
        }

        if (!empty($action)) {
            $this->db->bind('action', $action);
        }

        $this->db->bind('limit', $limit);

        return $this->db->resultSet();
    }

    public function getDistinctActions()
    {
        $this->db->query("SELECT DISTINCT action FROM " . $this->table . " ORDER BY action ASC");

        return $this->db->resultSet();
    }

    public function getAuditStatistics()
    {
        $sql = "SELECT COUNT(*) AS total_logs, COUNT(CASE WHEN action = 'DOCUMENT_SIGNED' THEN 1 END) AS total_signed, COUNT(CASE WHEN action = 'TRIAGE_ASSIGNED' THEN 1 END) AS total_triage, COUNT(CASE WHEN action IN ('REJECTED', 'DOCUMENT_CANCELLED', 'CANCELLED') THEN 1 END) AS total_rejected_or_cancelled FROM " . $this->table;

        $this->db->query($sql);

        return $this->db->single();
    }

    public function createLog($documentId, $userId, $action, $notes = '')
    {
        $this->db->query("INSERT INTO " . $this->table . " (document_id, user_id, action, notes) VALUES (:document_id, :user_id, :action, :notes)");

        $this->db->bind('document_id', $documentId);
        $this->db->bind('user_id', $userId);
        $this->db->bind('action', $action);
        $this->db->bind('notes', $notes);

        return $this->db->execute();
    }
}
