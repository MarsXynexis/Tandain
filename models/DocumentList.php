<?php

class DocumentList
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Syarat pencarian dan filter, dipakai bersama oleh hitung dan ambil data
    private function buildWhere($search, $filter)
    {
        $where = "WHERE d.requester_id = :user_id";

        if ($search !== '') {
            $where .= " AND (d.title ILIKE :search_title OR d.document_number ILIKE :search_number)";
        }

        if ($filter === 'completed') {
            $where .= " AND d.status = 'COMPLETED'";
        } elseif ($filter === 'in_progress') {
            $where .= " AND d.status IN ('PENDING_TRIAGE', 'IN_PROGRESS')";
        }

        return $where;
    }

    private function bindFilters($userId, $search)
    {
        $this->db->bind('user_id', $userId);

        if ($search !== '') {
            $this->db->bind('search_title', '%' . $search . '%');
            $this->db->bind('search_number', '%' . $search . '%');
        }
    }

    // Jumlah dokumen sesuai pencarian dan filter, untuk pagination
    public function countDocuments($userId, $search, $filter)
    {
        $sql = "SELECT COUNT(*) AS total FROM documents d " . $this->buildWhere($search, $filter);

        $this->db->query($sql);
        $this->bindFilters($userId, $search);

        $row = $this->db->single();

        return (int) $row['total'];
    }

    // Daftar dokumen milik user, satu halaman saja
    public function getDocuments($userId, $search, $filter, $limit, $offset)
    {
        $sql = "SELECT d.id, d.title, d.status, d.created_at, d.original_file_path, d.final_file_path,
                (SELECT COUNT(*) FROM document_signers s WHERE s.document_id = d.id) AS total_signers,
                (SELECT COUNT(*) FROM document_signers s WHERE s.document_id = d.id AND s.status = 'SIGNED') AS signed_count
                FROM documents d " . $this->buildWhere($search, $filter) . "
                ORDER BY d.created_at DESC
                LIMIT " . (int) $limit . " OFFSET " . (int) $offset;

        $this->db->query($sql);
        $this->bindFilters($userId, $search);

        return $this->db->resultSet();
    }

    // Angka di tab Semua, Selesai, dan Sedang Diproses
    public function getStatusCounts($userId)
    {
        $sql = "SELECT COUNT(*) AS total_all,
                COUNT(CASE WHEN status = 'COMPLETED' THEN 1 END) AS total_completed,
                COUNT(CASE WHEN status IN ('PENDING_TRIAGE', 'IN_PROGRESS') THEN 1 END) AS total_in_progress
                FROM documents WHERE requester_id = :user_id";

        $this->db->query($sql);
        $this->db->bind('user_id', $userId);

        return $this->db->single();
    }

    // Satu dokumen, hanya kalau milik user yang login
    public function getDocumentById($id, $userId)
    {
        // id berupa UUID, kalau formatnya salah PostgreSQL akan error
        if (!preg_match('/^[0-9a-f-]{36}$/i', $id)) {
            return false;
        }

        $this->db->query("SELECT * FROM documents WHERE id = :id AND requester_id = :user_id LIMIT 1");
        $this->db->bind('id', $id);
        $this->db->bind('user_id', $userId);

        return $this->db->single();
    }

    // Penanda tangan satu dokumen, urut sesuai giliran
    public function getSigners($documentId)
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
}