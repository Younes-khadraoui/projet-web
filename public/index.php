<?php
// public/index.php

// Session start 
session_start();

require_once __DIR__ . '/../app/core/database.php';

// Initialize Database
$database = new Database();
$db = $database->getConnection();

// Basic Routing logic
$action = isset($_GET['action']) ? $_GET['action'] : 'home';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>e-bazar | Petites Annonces</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; }
        .category-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }
        .category-item { border: 1px solid #ddd; padding: 15px; border-radius: 5px; text-align: center; }
    </style>
</head>
<body>
    <header>
        <h1>e-bazar</h1>
        <nav>
            <a href="index.php">Accueil</a> | 
            <a href="?action=login">Connexion</a>
        </nav>
    </header>

    <main>
        <?php if ($action === 'home'): ?>
            <h2>Catégories</h2>
            <div class="category-list">
                <?php
                // Fetch categories and count items
                $query = "SELECT c.id, c.name, COUNT(a.id) as total 
                          FROM categories c 
                          LEFT JOIN ads a ON c.id = a.category_id 
                          GROUP BY c.id";
                $stmt = $db->prepare($query);
                $stmt->execute();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="category-item">
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                        <?php echo $row['total']; ?> annonce(s)<br>
                        <a href="?action=list&category=<?php echo $row['id']; ?>">Voir les biens</a>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <hr>
            <h2>Derniers biens mis en vente</h2>
            <p><i>(Les 4 dernières annonces apparaîtront ici)</i></p>

        <?php elseif ($action === 'login'): ?>
            <h2>Connexion</h2>
            <p>Formulaire de connexion...</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 e-bazar - Projet Web M1</p>
    </footer>
</body>
</html>