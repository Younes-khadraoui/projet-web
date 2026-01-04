<?php

class AuthController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Handle user registration
     */
    public function register() {
        $email = '';
        $message = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Basic validation
            if (empty($name) || empty($email) || empty($password)) {
                $message = 'Veuillez remplir tous les champs.';
            } elseif ($password !== $confirmPassword) {
                $message = 'Les mots de passe ne correspondent pas.';
            } else {
                // Call User model to register
                require_once __DIR__ . '/../models/User.php';
                $userModel = new User($this->db);
                $result = $userModel->register($name, $email, $password);
                
                if ($result['success']) {
                    $success = true;
                    $message = $result['message'];
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    header('Location: ?action=home');
                    exit;
                } else {
                    $message = $result['message'];
                }
            }
        }

        return ['email' => $email, 'message' => $message, 'success' => $success];
    }

    /**
     * Handle user login
     */
    public function login() {
        $email = '';
        $message = '';
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $message = 'Veuillez remplir tous les champs.';
            } else {
                require_once __DIR__ . '/../models/User.php';
                $userModel = new User($this->db);
                $result = $userModel->login($email, $password);

                if ($result['success']) {
                    $success = true;
                    $message = $result['message'];
                    $_SESSION['user_id'] = $result['user']['id'];
                    $_SESSION['user_name'] = $result['user']['name'];
                    $_SESSION['user_email'] = $result['user']['email'];
                    $_SESSION['user_role'] = $result['user']['role'];
                    $_SESSION['user_balance'] = $result['user']['balance'];
                    
                    // Redirect to the page the user was trying to access, or to the dashboard
                    if (isset($_SESSION['redirect_after_login'])) {
                        $redirect_url = $_SESSION['redirect_after_login'];
                        unset($_SESSION['redirect_after_login']); // Clean up session
                        header('Location: ' . $redirect_url);
                    } else {
                        header('Location: ?action=dashboard'); // Default redirect
                    }
                    exit;
                } else {
                    $message = $result['message'];
                }
            }
        }

        return ['email' => $email, 'message' => $message, 'success' => $success];
    }

    /**
     * Handle user logout
     */
    public function logout() {
        session_destroy();
        header('Location: ?action=home');
        exit;
    }
}
