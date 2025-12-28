<?php
// scripts/test_db.php

require_once __DIR__ . '/../app/core/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    if ($db) {
        echo "Connexion réussie à MariaDB !\n";
        
        $stmt = $db->query("SELECT DATABASE()");
        $currentDb = $stmt->fetchColumn();
        echo "Base de données active : " . $currentDb . "\n";
    }
} catch (Exception $e) {
    echo "" . $e->getMessage() . "\n";
    echo "Conseil : Vérifiez que l'extension pdo_mysql est activée dans /etc/php/php.ini\n";
}