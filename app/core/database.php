<?php
// app/core/database.php

class Database {
    private $host = "localhost";
    private $db_name = "projet";
    private $username = "projet";
    private $password = "tejorp";
    public $conn;

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