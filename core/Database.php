<?php

class Database
{
    private $host;
    private $user;
    private $pass;
    private $db_name;
    private $dbh;
    private $stmt;

    public function __construct()
    {
        $env = parse_ini_file('.env');
        $this->host = $env['DB_HOST'];
        $this->user = $env['DB_USER'];
        $this->pass = $env['DB_PASS'];
        $this->db_name = $env['DB_NAME'];

        $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }

    public function bind($param, $value)
    {
        $this->stmt->bindValue($param, $value);
    }

    public function execute()
    {
        return $this->stmt->execute();
    }

    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
}
