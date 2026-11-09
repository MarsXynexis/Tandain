<?php

class User
{
    private $table = 'users';
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function findByEmail($email)
    {
        $this->db->query("SELECT users.*, roles.name AS role_name, roles.display_name AS role_display_name FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id WHERE LOWER(users.email) = LOWER(:email) LIMIT 1");

        $this->db->bind('email', $email);

        return $this->db->single();
    }

    public function findByUsername($username)
    {
        $this->db->query("SELECT users.*, roles.name AS role_name, roles.display_name AS role_display_name FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id WHERE LOWER(users.email) = LOWER(:username) OR users.identity_number = :username LIMIT 1");

        $this->db->bind('username', $username);

        return $this->db->single();
    }

    public function findByIdentityNumber($identityNumber)
    {
        $this->db->query("SELECT users.*, roles.name AS role_name, roles.display_name AS role_display_name FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id WHERE users.identity_number = :identity_number LIMIT 1");

        $this->db->bind('identity_number', $identityNumber);

        return $this->db->single();
    }

    public function findById($id)
    {
        $this->db->query("SELECT users.*, roles.name AS role_name, roles.display_name AS role_display_name FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id WHERE users.id = :id LIMIT 1");

        $this->db->bind('id', $id);

        return $this->db->single();
    }

    public function findRoleByName($name)
    {
        $this->db->query("SELECT id FROM roles WHERE name = :name LIMIT 1");

        $this->db->bind('name', $name);

        return $this->db->single();
    }

    public function getAllRoles()
    {
        $this->db->query("SELECT id, name, display_name FROM roles ORDER BY name ASC");

        return $this->db->resultSet();
    }

    public function create($data)
    {
        $position = $data['position'] ?? null;

        $this->db->query("INSERT INTO " . $this->table . " (role_id, full_name, identity_number, position, email, password_hash) VALUES (:role_id, :full_name, :identity_number, :position, :email, :password_hash)");

        $this->db->bind('role_id', $data['role_id']);
        $this->db->bind('full_name', $data['full_name']);
        $this->db->bind('identity_number', $data['identity_number']);
        $this->db->bind('position', $position);
        $this->db->bind('email', $data['email']);
        $this->db->bind('password_hash', $data['password_hash']);

        return $this->db->execute();
    }

    public function updateUser($id, $data)
    {
        $this->db->query("UPDATE " . $this->table . " SET full_name = :full_name, identity_number = :identity_number, position = :position, email = :email, role_id = :role_id, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id");

        $this->db->bind('id', $id);
        $this->db->bind('full_name', $data['full_name']);
        $this->db->bind('identity_number', $data['identity_number']);
        $this->db->bind('position', $data['position'] ?? null);
        $this->db->bind('email', $data['email']);
        $this->db->bind('role_id', $data['role_id']);
        $this->db->bind('status', $data['status']);

        return $this->db->execute();
    }

    public function toggleStatus($id)
    {
        $user = $this->findById($id);

        if ($user) {
            $newStatus = ($user['status'] === 'ACTIVE') ? 'SUSPENDED' : 'ACTIVE';

            $this->db->query("UPDATE " . $this->table . " SET status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id");

            $this->db->bind('id', $id);
            $this->db->bind('status', $newStatus);

            $result = $this->db->execute();
        } else {
            $result = false;
        }

        return $result;
    }

    public function resetPassword($id, $passwordHash)
    {
        $this->db->query("UPDATE " . $this->table . " SET password_hash = :password_hash, updated_at = CURRENT_TIMESTAMP WHERE id = :id");

        $this->db->bind('id', $id);
        $this->db->bind('password_hash', $passwordHash);

        return $this->db->execute();
    }

    public function getAssignableSignersByIds($ids)
    {
        if (empty($ids)) {
            $rows = [];
        } else {
            $placeholders = [];

            foreach ($ids as $index => $id) {
                $placeholders[] = ':id' . $index;
            }

            $sql = "SELECT users.id FROM " . $this->table . " AS users JOIN signer_certificates sc ON sc.user_id = users.id AND sc.status = 'ACTIVE' WHERE users.status = 'ACTIVE' AND users.id IN (" . implode(', ', $placeholders) . ")";

            $this->db->query($sql);

            foreach ($ids as $index => $id) {
                $this->db->bind('id' . $index, $id);
            }

            $rows = $this->db->resultSet();
        }

        return $rows;
    }

    public function searchActiveUsers($keyword, $limit = 8)
    {
        $sql = "SELECT users.id, users.full_name, users.identity_number, users.email, users.position, roles.name AS role_name, roles.display_name AS role_display_name, CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END AS has_active_cert FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id LEFT JOIN signer_certificates sc ON sc.user_id = users.id AND sc.status = 'ACTIVE' WHERE users.status = 'ACTIVE' AND (users.full_name ILIKE :keyword OR users.identity_number ILIKE :keyword OR users.email ILIKE :keyword) ORDER BY users.full_name ASC LIMIT :limit";

        $this->db->query($sql);

        $this->db->bind('keyword', '%' . $keyword . '%');
        $this->db->bind('limit', $limit);

        return $this->db->resultSet();
    }

    public function getFilteredUsers($search = '', $roleName = '', $status = '')
    {
        $sql = "SELECT users.id, users.full_name, users.identity_number, users.email, users.position, users.status, users.created_at, roles.name AS role_name, roles.display_name AS role_display_name, CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END AS has_active_cert FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id LEFT JOIN signer_certificates sc ON sc.user_id = users.id AND sc.status = 'ACTIVE' WHERE 1=1";

        if (!empty($search)) {
            $sql .= " AND (users.full_name ILIKE :search OR users.identity_number ILIKE :search OR users.email ILIKE :search)";
        }

        if (!empty($roleName)) {
            $sql .= " AND roles.name = :role_name";
        }

        if (!empty($status)) {
            $sql .= " AND users.status = :status";
        }

        $sql .= " ORDER BY users.created_at DESC";

        $this->db->query($sql);

        if (!empty($search)) {
            $this->db->bind('search', '%' . $search . '%');
        }

        if (!empty($roleName)) {
            $this->db->bind('role_name', $roleName);
        }

        if (!empty($status)) {
            $this->db->bind('status', $status);
        }

        return $this->db->resultSet();
    }

    public function getUserStatistics()
    {
        $sql = "SELECT COUNT(*) AS total_users, COUNT(CASE WHEN roles.name = 'pegawai' THEN 1 END) AS total_pegawai, COUNT(CASE WHEN roles.name = 'mahasiswa' THEN 1 END) AS total_mahasiswa, COUNT(CASE WHEN users.status = 'ACTIVE' THEN 1 END) AS total_active FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id";

        $this->db->query($sql);

        return $this->db->single();
    }

    public function getSignerCandidates()
    {
        $sql = "SELECT u.id, u.full_name, u.identity_number, u.email, u.position FROM " . $this->table . " u JOIN roles r ON r.id = u.role_id WHERE r.name = 'pegawai' AND u.status = 'ACTIVE' ORDER BY u.full_name ASC";

        $this->db->query($sql);

        return $this->db->resultSet();
    }
}
