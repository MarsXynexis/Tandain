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
        $this->db->query("SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1");

        $this->db->bind('email', $email);

        return $this->db->single();
    }

    public function findById($id)
    {
        $this->db->query("SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1");

        $this->db->bind('id', $id);

        return $this->db->single();
    }

    public function findRoleByName($name)
    {
        $this->db->query("SELECT id FROM roles WHERE name = :name LIMIT 1");

        $this->db->bind('name', $name);

        return $this->db->single();
    }

    public function create($data)
    {
        $this->db->query("INSERT INTO " . $this->table . " (id, role_id, full_name, identity_number, email, password_hash) VALUES (UUID(), :role_id, :full_name, :identity_number, :email, :password_hash)");

        $this->db->bind('role_id', $data['role_id']);
        $this->db->bind('full_name', $data['full_name']);
        $this->db->bind('identity_number', $data['identity_number']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('password_hash', $data['password_hash']);

        return $this->db->execute();
    }
}
