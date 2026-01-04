<?php
// public/index.php

// Handle installation process if .env file is missing
if (!file_exists(__DIR__ . '/../.env')) {
    // If the request is for the installer, load it and exit
    if (strpos($_SERVER['REQUEST_URI'], '/install/index.php') !== false) {
        require __DIR__ . '/../install/index.php';
        exit;
    } else {
        // Otherwise, redirect to the installer
        header('Location: ../install/index.php');
        exit;
    }
}

// Session start 
session_start();

require_once __DIR__ . '/../app/core/config.php';
require_once __DIR__ . '/../app/core/database.php';
require_once __DIR__ . '/../app/core/helpers.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/CategoryController.php';
require_once __DIR__ . '/../app/controllers/AdController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/models/Ad.php';
require_once __DIR__ . '/../app/models/Transaction.php';
require_once __DIR__ . '/../app/models/User.php';

// Initialize Database
$database = new Database();
$db = $database->getConnection();

// Basic Routing logic
$action = isset($_GET['action']) ? $_GET['action'] : 'home';
$auth_data = null;
$current_user = getCurrentUser();

// Determine base URL for assets
$base_url = Config::get('BASE_URL', '');
if (empty($base_url)) {
    // Auto-detect if not set in .env
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base_url = $protocol . '://' . $host;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Bazar</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
</head>
<body>
    <header>
        <div class="header-container">
            <h1><a href="/" style="text-decoration: none; color: var(--primary);">e-bazar</a></h1>
            <nav>
                <a href="/">Accueil</a>
                <form action="/" method="GET" class="search-bar">
                    <input type="hidden" name="action" value="search">
                    <input type="text" name="q" placeholder="Rechercher des annonces..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
                <?php if (isLoggedIn()): ?>
                    <span class="nav-user">| Bienvenue, <?php echo escape($current_user['name']); ?></span>
                    <span class="nav-balance" onclick="openModal('topUpModal')" style="cursor: pointer;">| Solde: <strong><?php echo formatPrice($current_user['balance']); ?></strong></span>
                    <a href="?action=dashboard">Mon espace</a>
                    <?php if ($current_user['role'] === 'admin'): ?>
                        <a href="?action=admin">Admin</a>
                    <?php endif; ?>
                    <a href="?action=logout">Déconnexion</a>
                <?php else: ?>
                    <a href="?action=login">| Connexion</a>
                    <a href="?action=register">| S'inscrire</a>
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
                $result = $adController->create();
                $categories = $result['categories'];
                $errors = $result['errors'];
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

        <?php elseif ($action === 'ad'): ?>
            <?php
                $ad_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
                
                if ($ad_id > 0) {
                    $adModel = new Ad($db);
                    $ad = $adModel->getById($ad_id);
                    
                    if ($ad) {
                        // Get photos for this ad
                        $stmt = $db->prepare('SELECT * FROM photos WHERE ad_id = ? ORDER BY is_primary DESC, id ASC');
                        $stmt->execute([$ad_id]);
                        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $ad = null;
                        $photos = [];
                    }
                } else {
                    $ad = null;
                    $photos = [];
                }
            ?>
            <?php include __DIR__ . '/../app/views/ad.php'; ?>

        <?php elseif ($action === 'buy'): ?>
            <?php
                requireLogin(); // This will handle redirecting if the user is not logged in

                $ad_id = isset($_POST['ad_id']) ? (int)$_POST['ad_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
                
                // If this is a GET request, it means the user just logged in.
                // We show them the ad page again so they can confirm the purchase.
                if ($_SERVER['REQUEST_METHOD'] === 'GET' && $ad_id > 0) {
                    header('Location: ?action=ad&id=' . $ad_id);
                    exit;
                }

                $delivery_type = isset($_POST['delivery_type']) ? trim($_POST['delivery_type']) : '';
                
                if ($ad_id > 0 && !empty($delivery_type)) {
                    $adModel = new Ad($db);
                    $ad = $adModel->getById($ad_id);
                    
                    if ($ad && !$ad['is_sold']) {
                        // Process payment
                        $transactionModel = new Transaction($db);
                        $result = $transactionModel->processPurchase(
                            $ad_id,
                            $_SESSION['user_id'],
                            $ad['seller_id'],
                            $ad['price']
                        );
                        
                        if ($result['success']) {
                            // Mark ad as sold
                            $adModel->markAsSold($ad_id, $_SESSION['user_id']);
                            
                            // Update session balance directly
                            $_SESSION['user_balance'] -= $ad['price'];
                            
                            $_SESSION['purchase_message'] = 'Achat réussi!';
                            header('Location: ?action=dashboard');
                            exit;
                        } else {
                            // Payment failed
                            $_SESSION['purchase_error'] = $result['message'];
                            header('Location: /?action=ad&id=' . $ad_id);
                            exit;
                        }
                    }
                }
                
                // If we get here, something went wrong
                header('Location: /?action=ad&id=' . $ad_id);
                exit;
            ?>

        <?php elseif ($action === 'delete-ad'): ?>
            <?php
                requireLogin();
                $ad_id = isset($_POST['ad_id']) ? (int)$_POST['ad_id'] : 0;
                
                if ($ad_id > 0) {
                    $adModel = new Ad($db);
                    
                    // Check ownership
                    if ($adModel->isOwner($ad_id, $_SESSION['user_id'])) {
                        $ad = $adModel->getById($ad_id);
                        
                        // Allow deletion only if not sold or if owner
                        if ($ad && !$ad['is_sold']) {
                            $adModel->delete($ad_id);
                            header('Location: /?action=dashboard');
                            exit;
                        }
                    }
                }
                
                // If we get here, permission denied or ad not found
                header('Location: /?action=dashboard');
                exit;
            ?>

        <?php elseif ($action === 'confirm-receipt'): ?>
            <?php
                requireLogin();
                $ad_id = isset($_POST['ad_id']) ? (int)$_POST['ad_id'] : 0;
                
                if ($ad_id > 0) {
                    $adModel = new Ad($db);
                    $ad = $adModel->getById($ad_id);
                    
                    // Check if user is the buyer
                    if ($ad && $ad['buyer_id'] == $_SESSION['user_id']) {
                        // Mark as received but don't delete
                        $adModel->markAsReceived($ad_id);
                        
                        header('Location: /?action=dashboard');
                        exit;
                    }
                }
                
                // If we get here, permission denied
                header('Location: /?action=dashboard');
                exit;
            ?>

        <?php elseif ($action === 'dashboard'): ?>
            <?php
                requireLogin();
                $adModel = new Ad($db);
                
                $for_sale_ads = $adModel->getForSaleByUser($_SESSION['user_id']);
                $sold_ads = $adModel->getSoldByUser($_SESSION['user_id']);
                $purchased_ads = $adModel->getPurchasedByUser($_SESSION['user_id']);
            ?>
            <?php include __DIR__ . '/../app/views/dashboard.php'; ?>

        <?php elseif ($action === 'admin'): ?>
            <?php
                requireAdmin();
                $adminController = new AdminController($db);
                
                $ads = $adminController->getAllAds();
                $users = $adminController->getAllUsers();
                $categories = $adminController->getAllCategories();
            ?>
            <?php include __DIR__ . '/../app/views/admin.php'; ?>

        <?php elseif ($action === 'admin-delete-ad'): ?>
            <?php
                requireAdmin();
                $ad_id = isset($_POST['ad_id']) ? (int)$_POST['ad_id'] : 0;
                
                if ($ad_id > 0) {
                    $adminController = new AdminController($db);
                    $result = $adminController->deleteAd($ad_id);
                    
                    // Redirect with message
                    $_SESSION['admin_message'] = $result['message'];
                    $_SESSION['admin_message_type'] = $result['success'] ? 'success' : 'error';
                }
                
                header('Location: /?action=admin');
                exit;
            ?>

        <?php elseif ($action === 'admin-delete-user'): ?>
            <?php
                requireAdmin();
                $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
                
                if ($user_id > 0) {
                    $adminController = new AdminController($db);
                    $result = $adminController->deleteUser($user_id);
                    
                    // Redirect with message
                    $_SESSION['admin_message'] = $result['message'];
                    $_SESSION['admin_message_type'] = $result['success'] ? 'success' : 'error';
                }
                
                header('Location: /?action=admin#users');
                exit;
            ?>

        <?php elseif ($action === 'admin-add-category'): ?>
            <?php
                requireAdmin();
                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                
                if (!empty($name)) {
                    $adminController = new AdminController($db);
                    $result = $adminController->addCategory($name);
                    
                    $_SESSION['admin_message'] = $result['message'];
                    $_SESSION['admin_message_type'] = $result['success'] ? 'success' : 'error';
                }
                
                header('Location: /?action=admin#categories');
                exit;
            ?>

        <?php elseif ($action === 'admin-rename-category'): ?>
            <?php
                requireAdmin();
                $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
                $name = isset($_POST['name']) ? trim($_POST['name']) : '';
                
                if ($category_id > 0 && !empty($name)) {
                    $adminController = new AdminController($db);
                    $result = $adminController->renameCategory($category_id, $name);
                    
                    $_SESSION['admin_message'] = $result['message'];
                    $_SESSION['admin_message_type'] = $result['success'] ? 'success' : 'error';
                }
                
                header('Location: /?action=admin#categories');
                exit;
            ?>

        <?php elseif ($action === 'admin-delete-category'): ?>
            <?php
                requireAdmin();
                $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
                
                if ($category_id > 0) {
                    $adminController = new AdminController($db);
                    $result = $adminController->deleteCategory($category_id);
                    
                    $_SESSION['admin_message'] = $result['message'];
                    $_SESSION['admin_message_type'] = $result['success'] ? 'success' : 'error';
                }
                
                header('Location: /?action=admin#categories');
                exit;
            ?>

        <?php elseif ($action === 'top-up-balance'): ?>
            <?php
                requireLogin();
                $amount = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
                
                if ($amount > 0) {
                    $transactionModel = new Transaction($db);
                    $result = $transactionModel->topUpBalance($_SESSION['user_id'], $amount);
                    
                    $_SESSION['topup_message'] = $result['message'];
                    $_SESSION['topup_success'] = $result['success'];
                    
                    if ($result['success']) {
                        // More reliable: directly update the session balance
                        $userModel = new User($db);
                        $current_balance = $_SESSION['user_balance'] ?? 0;
                        $new_balance = $current_balance + $amount;
                        $_SESSION['user_balance'] = $userModel->updateBalance($_SESSION['user_id'], $new_balance);
                    }
                }
                
                header('Location: /?action=dashboard');
                exit;
            ?>

        <?php elseif ($action === 'search'): ?>
            <?php
                $search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $per_page = 10; // Number of search results per page

                $adModel = new Ad($db);
                $search_results = ['ads' => [], 'total' => 0];

                if (!empty($search_query)) {
                    $search_results = $adModel->search($search_query, $page, $per_page);
                }

                $ads = $search_results['ads'];
                $total_count = $search_results['total'];
                $total_pages = ceil($total_count / $per_page);
            ?>
            <?php include __DIR__ . '/../app/views/search.php'; ?>

        <?php endif; ?>
        </div>
    </main>

    <?php if (isLoggedIn()): ?>
    <!-- Top Up Balance Modal -->
    <div id="topUpModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('topUpModal')">&times;</span>
            <h2>Créditer votre solde</h2>
            <form method="POST" action="?action=top-up-balance">
                <div class="form-group">
                    <label for="topUpAmount">Montant à créditer (€)</label>
                    <input type="number" id="topUpAmount" name="amount" step="0.01" min="0.01" max="10000" placeholder="Ex: 50.00" required>
                    <small>Montant minimum: 0.01 € | Maximum: 10 000 €</small>
                </div>
                <div class="form-group" style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('topUpModal')">Annuler</button>
                    <button type="submit" class="btn btn-success">Créditer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.add('show');
        }
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) modal.classList.remove('show');
        }
    </script>
    <?php endif; ?>

</body>
</html>