<?php
// public/index.php

// Session start 
session_start();

require_once __DIR__ . '/../app/core/database.php';
require_once __DIR__ . '/../app/core/helpers.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/CategoryController.php';
require_once __DIR__ . '/../app/controllers/AdController.php';
require_once __DIR__ . '/../app/models/Ad.php';

// Initialize Database
$database = new Database();
$db = $database->getConnection();

// Basic Routing logic
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
$auth_data = null;
$current_user = getCurrentUser();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>e-bazar | Petites Annonces</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1>e-bazar</h1>
            <nav>
                <a href="index.php">Accueil</a> | 
                <?php if (isLoggedIn()): ?>
                    <span>Bienvenue <?php echo escape($current_user['name']); ?></span> |
                    <a href="?action=dashboard">Mon espace</a> |
                    <?php if ($current_user['role'] === 'admin'): ?>
                        <a href="?action=admin">Admin</a> |
                    <?php endif; ?>
                    <a href="?action=logout">Déconnexion</a>
                <?php else: ?>
                    <a href="?action=login">Connexion</a> |
                    <a href="?action=register">S'inscrire</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
        <?php if ($action === 'home'): ?>
            <?php
                $categoryController = new CategoryController($db);
                $adModel = new Ad($db);
                
                $categories = $categoryController->getAllWithCounts();
                $recent_ads = $adModel->getRecent(4);
            ?>
            <?php include __DIR__ . '/../app/views/home.php'; ?>

        <?php elseif ($action === 'login'): ?>
            <?php
                $authController = new AuthController($db);
                $auth_data = $authController->login();
            ?>
            <?php include __DIR__ . '/../app/views/login.php'; ?>

        <?php elseif ($action === 'register'): ?>
            <?php
                $authController = new AuthController($db);
                $auth_data = $authController->register();
            ?>
            <?php include __DIR__ . '/../app/views/register.php'; ?>

        <?php elseif ($action === 'logout'): ?>
            <?php
                $authController = new AuthController($db);
                $authController->logout();
            ?>

        <?php elseif ($action === 'create-ad'): ?>
            <?php
                requireLogin();
                $adController = new AdController($db);
                $categoryController = new CategoryController($db);
                $categories = $categoryController->getAll();
                $adController->create();
            ?>
            <?php include __DIR__ . '/../app/views/create-ad.php'; ?>

        <?php elseif ($action === 'category'): ?>
            <?php
                $category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                
                if ($category_id > 0) {
                    $categoryController = new CategoryController($db);
                    $adModel = new Ad($db);
                    
                    $category = $categoryController->getById($category_id);
                    if ($category) {
                        $result = $adModel->getByCategory($category_id, $page, 10);
                        $ads = $result['ads'];
                        $total_count = $result['total'];
                        $total_pages = ceil($total_count / 10);
                    } else {
                        $ads = [];
                        $total_pages = 0;
                    }
                } else {
                    $ads = [];
                    $category = null;
                    $total_pages = 0;
                }
            ?>
            <?php if ($category): ?>
                <?php include __DIR__ . '/../app/views/category.php'; ?>
            <?php else: ?>
                <p><em>Catégorie non trouvée.</em></p>
            <?php endif; ?>

        <?php elseif ($action === 'dashboard'): ?>
            <?php
                requireLogin();
                $adModel = new Ad($db);
                
                $for_sale_ads = $adModel->getForSaleByUser($_SESSION['user_id']);
                $sold_ads = $adModel->getSoldByUser($_SESSION['user_id']);
                $purchased_ads = $adModel->getPurchasedByUser($_SESSION['user_id']);
            ?>
            <?php include __DIR__ . '/../app/views/dashboard.php'; ?>

        <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 e-bazar - Projet Web M1</p>
    </footer>
</body>
</html>