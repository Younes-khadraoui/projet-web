<?php

class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Register a new user
     * @param string $name
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user_id' => int|null]
     */
    public function register($name, $email, $password) {
        // Validate name
        if (empty($name) || strlen($name) < 2) {
            return ['success' => false, 'message' => 'Le nom doit contenir au minimum 2 caractères.'];
        }

        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email invalide.'];
        }

        // Validate password length
        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Le mot de passe doit contenir au minimum 6 caractères.'];
        }

        // Check if email already exists
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Cet email est déjà utilisé.'];
        }

        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Insert user
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())'
            );
            $stmt->execute([$name, $email, $passwordHash, 'user']);
            $user_id = $this->db->lastInsertId();
            return ['success' => true, 'message' => 'Inscription réussie!', 'user_id' => $user_id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de l\'inscription: ' . $e->getMessage()];
        }
    }

    /**
     * Authenticate user by email and password
     * @param string $email
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'user' => array|null]
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Email et mot de passe requis.'];
        }

        $stmt = $this->db->prepare('SELECT id, name, email, password, role FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
        }

        // Remove password from returned user data for security
        unset($user['password']);
        return ['success' => true, 'message' => 'Connexion réussie!', 'user' => $user];
    }

    /**
     * Get user by ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $stmt = $this->db->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
