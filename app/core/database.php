<?php
// app/core/database.php

require_once __DIR__ . '/config.php';

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host = Config::get('DB_HOST', 'localhost');
        $this->db_name = Config::get('DB_NAME', 'projet');
        $this->username = Config::get('DB_USER', 'projet');
        $this->password = Config::get('DB_PASSWORD', 'tejorp');
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            throw new Exception("Erreur de connexion : " . $exception->getMessage());
        }

        return $this->conn;
    }
}