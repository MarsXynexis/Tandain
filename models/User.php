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
        $user = false;

        if (isValidUuid($id)) {
            $this->db->query("SELECT users.*, roles.name AS role_name, roles.display_name AS role_display_name FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id WHERE users.id = :id LIMIT 1");

            $this->db->bind('id', $id);

            $user = $this->db->single();
        }

        return $user;
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
        $rawPosition = trim((string) ($data['position'] ?? ''));
        $position = $rawPosition !== '' ? $rawPosition : null;
        $rawStudyProgram = trim((string) ($data['study_program'] ?? ''));
        $studyProgram = $rawStudyProgram !== '' ? $rawStudyProgram : null;

        $this->db->query("INSERT INTO " . $this->table . " (role_id, full_name, identity_number, position, study_program, email, password_hash) VALUES (:role_id, :full_name, :identity_number, :position, :study_program, :email, :password_hash)");

        $this->db->bind('role_id', $data['role_id']);
        $this->db->bind('full_name', $data['full_name']);
        $this->db->bind('identity_number', $data['identity_number']);
        $this->db->bind('position', $position);
        $this->db->bind('study_program', $studyProgram);
        $this->db->bind('email', $data['email']);
        $this->db->bind('password_hash', $data['password_hash']);

        return $this->db->execute();
    }

    public function updateUser($id, $data)
    {
        $updated = false;

        if (isValidUuid($id) && isValidUuid($data['role_id'] ?? '')) {
            $rawPosition = trim((string) ($data['position'] ?? ''));
            $position = $rawPosition !== '' ? $rawPosition : null;
            $rawStudyProgram = trim((string) ($data['study_program'] ?? ''));
            $studyProgram = $rawStudyProgram !== '' ? $rawStudyProgram : null;

            $this->db->query("UPDATE " . $this->table . " SET full_name = :full_name, identity_number = :identity_number, position = :position, study_program = :study_program, email = :email, role_id = :role_id, status = :status, updated_at = CURRENT_TIMESTAMP WHERE id = :id");

            $this->db->bind('id', $id);
            $this->db->bind('full_name', $data['full_name']);
            $this->db->bind('identity_number', $data['identity_number']);
            $this->db->bind('position', $position);
            $this->db->bind('study_program', $studyProgram);
            $this->db->bind('email', $data['email']);
            $this->db->bind('role_id', $data['role_id']);
            $this->db->bind('status', $data['status']);

            $updated = $this->db->execute();
        }

        return $updated;
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
        $updated = false;

        if (isValidUuid($id)) {
            $this->db->query("UPDATE " . $this->table . " SET password_hash = :password_hash, updated_at = CURRENT_TIMESTAMP WHERE id = :id");

            $this->db->bind('id', $id);
            $this->db->bind('password_hash', $passwordHash);

            $updated = $this->db->execute();
        }

        return $updated;
    }

    public function updateSignatureImagePath($id, $path)
    {
        $updated = false;

        if (isValidUuid($id)) {
            $this->db->query("UPDATE " . $this->table . " SET signature_image_path = :path, updated_at = CURRENT_TIMESTAMP WHERE id = :id");

            $this->db->bind('id', $id);
            $this->db->bind('path', $path);

            $updated = $this->db->execute();
        }

        return $updated;
    }

    public function getAssignableSignersByIds($ids)
    {
        $rows = [];
        $validIds = array_filter($ids, static function ($id) {
            return isValidUuid($id);
        });

        if ($ids && $validIds === $ids) {
            $placeholders = [];

            foreach ($ids as $index => $id) {
                $placeholders[] = ':id' . $index;
            }

            $sql = "SELECT DISTINCT users.id
                    FROM " . $this->table . " AS users
                    JOIN roles ON roles.id = users.role_id
                    JOIN signer_certificates sc ON sc.user_id = users.id
                        AND sc.status = 'ACTIVE'
                        AND CURRENT_TIMESTAMP BETWEEN sc.valid_from AND sc.valid_until
                    WHERE users.status = 'ACTIVE'
                        AND roles.name <> 'admin_tu'
                        AND users.id IN (" . implode(', ', $placeholders) . ")";

            $this->db->query($sql);

            foreach ($ids as $index => $id) {
                $this->db->bind('id' . $index, $id);
            }

            $rows = $this->db->resultSet();
        }

        return $rows;
    }

    public function searchSignerCandidates($keyword)
    {
        $sql = "SELECT DISTINCT users.id, users.full_name, users.identity_number, users.email, users.position, users.study_program,
                    roles.display_name AS role_display_name,
                    CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END AS has_active_cert
                FROM " . $this->table . " AS users
                JOIN roles ON roles.id = users.role_id
                LEFT JOIN signer_certificates sc ON sc.user_id = users.id
                    AND sc.status = 'ACTIVE'
                    AND CURRENT_TIMESTAMP BETWEEN sc.valid_from AND sc.valid_until
                WHERE users.status = 'ACTIVE'
                    AND roles.name <> 'admin_tu'
                    AND (users.full_name ILIKE :name ESCAPE '!'
                         OR users.identity_number ILIKE :identity ESCAPE '!'
                         OR users.email ILIKE :email ESCAPE '!'
                         OR COALESCE(users.position, '') ILIKE :position ESCAPE '!'
                         OR COALESCE(users.study_program, '') ILIKE :study_program ESCAPE '!')
                ORDER BY has_active_cert DESC, users.full_name ASC, users.id ASC LIMIT 8";

        $this->db->query($sql);

        $pattern = '%' . strtr($keyword, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
        $this->db->bind('name', $pattern);
        $this->db->bind('identity', $pattern);
        $this->db->bind('email', $pattern);
        $this->db->bind('position', $pattern);
        $this->db->bind('study_program', $pattern);

        return $this->db->resultSet();
    }

    public function getFilteredUsers($search = '', $roleName = '', $status = '')
    {
        $sql = "SELECT users.id, users.full_name, users.identity_number, users.email, users.position, users.study_program, users.status, users.created_at, roles.name AS role_name, roles.display_name AS role_display_name, CASE WHEN sc.id IS NOT NULL THEN 1 ELSE 0 END AS has_active_cert FROM " . $this->table . " AS users JOIN roles ON roles.id = users.role_id LEFT JOIN signer_certificates sc ON sc.user_id = users.id AND sc.status = 'ACTIVE' WHERE 1=1";

        if (!empty($search)) {
            $sql .= " AND (users.full_name ILIKE :search OR users.identity_number ILIKE :search OR users.email ILIKE :search OR COALESCE(users.position, '') ILIKE :search OR COALESCE(users.study_program, '') ILIKE :search)";
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
}
