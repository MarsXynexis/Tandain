<?php

class DocumentSigner
{
    private $table = 'document_signers';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getByDocumentId($documentId)
    {
        $sql = "SELECT ds.*, u.full_name AS signer_name, u.identity_number AS signer_nim_nip, u.email AS signer_email, u.position AS signer_position, CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END AS has_active_cert FROM " . $this->table . " ds JOIN users u ON u.id = ds.signer_id LEFT JOIN signer_certificates sc ON sc.user_id = u.id AND sc.status = 'ACTIVE' WHERE ds.document_id = :document_id ORDER BY ds.signing_order ASC";

        $this->db->query($sql);
        $this->db->bind('document_id', $documentId);

        return $this->db->resultSet();
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
