<?php
// install/index.php
error_reporting(E_ALL & ~E_NOTICE);

$env_path = __DIR__ . '/../.env';
$is_installed = file_exists($env_path);
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error_message = null;

if ($is_installed && !isset($_GET['force'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $step === 1) {
    $db_host = trim($_POST['db_host']);
    $db_name = trim($_POST['db_name']);
    $db_user = trim($_POST['db_user']);
    $db_pass = trim($_POST['db_pass']);
    $seed_data = isset($_POST['seed_data']);

    try {
        $dsn = "mysql:host=$db_host";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (PDOException $e) {
        $error_message = "Échec de la connexion : " . $e->getMessage();
    }

    if (!$error_message) {
        try {
            // Create database if it doesn't exist
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `$db_name`");

            // Read and execute schema.sql
            $schema_sql = file_get_contents(__DIR__ . '/schema.sql');
            if ($schema_sql === false) {
                throw new Exception("Impossible de lire le fichier schema.sql.");
            }

            $pdo->exec($schema_sql);

            // Optionally, execute data.sql for seeding
            if ($seed_data) {
                $data_sql = file_get_contents(__DIR__ . '/data.sql');
                if ($data_sql === false) {
                    throw new Exception("Impossible de lire le fichier data.sql.");
                }
                $pdo->exec($data_sql);
            }

            // Create .env file
            $env_content = "DB_HOST=$db_host\n";
            $env_content .= "DB_NAME=$db_name\n";
            $env_content .= "DB_USER=$db_user\n";
            $env_content .= "DB_PASS=$db_pass\n";
            $env_content .= "BASE_URL=\n"; 

            if (file_put_contents($env_path, $env_content) === false) {
                throw new Exception("Impossible de créer le fichier .env. Veuillez vérifier les permissions.");
            }

            header("Location: index.php?step=2");
            exit;

        } catch (Exception $e) {
            $error_message = "Erreur d'installation : " . $e->getMessage();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Installation E-Bazar</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; background-color: #f4f7f6; color: #333; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .installer-box { background: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 450px; padding: 40px; }
        h1 { text-align: center; color: #2c3e50; margin-top: 0; }
        .form-group { margin-bottom: 20px; }
        label, .checkbox-label { font-weight: 600; display: block; margin-bottom: 8px; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type="text"]::placeholder { color: #aaa; }
        .btn { display: block; width: 100%; background-color: #28a745; color: white; padding: 15px; border: none; border-radius: 4px; font-size: 16px; font-weight: bold; cursor: pointer; text-align: center; text-decoration: none; }
        .btn:hover { background-color: #218838; }
        .alert { padding: 15px; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px; margin-bottom: 20px; }
        .success-box { text-align: center; }
        .success-box p { line-height: 1.6; }
        .note { font-size: 0.9em; color: #777; background: #f0f0f0; padding: 10px; border-radius: 4px; margin-top: 15px; }
    </style>
</head>
<body>

    <div class="installer-box">
        <?php if ($step === 1): ?>
            <h1>Installation & Configuration</h1>
            
            <?php if ($error_message): ?>
                <div class="alert"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?step=1">
                <div class="form-group">
                    <label for="db_host">Serveur de base de données (Host)</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label for="db_name">Nom de la base de données</label>
                    <input type="text" id="db_name" name="db_name" placeholder="ex: projet" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Utilisateur de la base</label>
                    <input type="text" id="db_user" name="db_user" placeholder="ex: projet" required>
                </div>
                <div class="form-group">
                    <label for="db_pass">Mot de passe de la base</label>
                    <input type="password" id="db_pass" name="db_pass">
                </div>
                <div class="form-group">
                    <input type="checkbox" id="seed_data" name="seed_data" checked>
                    <label for="seed_data" class="checkbox-label" style="display: inline;">Insérer des données de test (Utilisateurs, Annonces...)</label>
                </div>
                <button type="submit" class="btn">Lancer l'installation</button>
            </form>
        <?php elseif ($step === 2): ?>
            <div class="success-box">
                <h1>Installation Réussie!</h1>
                <p>La base de données et le fichier de configuration ont été créés avec succès.</p>
                <!-- Corrected to link to the root index.php -->
                <a href="../index.php" class="btn">Accéder au site</a>
                <p class="note"><strong>Important:</strong> Pour des raisons de sécurité, veuillez supprimer le dossier <code>/install</code> de votre serveur.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
