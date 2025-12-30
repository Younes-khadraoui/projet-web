<?php

/**
 * Helper functions for e-bazar application
 */

/**
 * Require user to be logged in
 * Redirects to login page if not authenticated
 * @param string $redirect Optional: page to redirect to after login (e.g., "?action=create_ad")
 */
function requireLogin($redirect = null) {
    if (!isset($_SESSION['user_id'])) {
        // Store the intended destination
        if ($redirect === null) {
            $redirect = $_SERVER['REQUEST_URI'];
        }
        $_SESSION['redirect_after_login'] = $redirect;
        header('Location: ?action=login&redirect=' . urlencode($redirect));
        exit;
    }
}

/**
 * Require user to be an admin
 * Redirects to home page if not admin
 */
function requireAdmin() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: ?action=home');
        exit;
    }
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current user info from session
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role'] ?? 'user'
    ];
}

/**
 * Escape HTML output to prevent XSS
 * @param string $text
 * @return string
 */
function escape($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Format price for display
 * @param float $price
 * @return string
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Translate delivery type to French
 * @param string $type (postal, hand, both)
 * @return string
 */
function getDeliveryLabel($type) {
    $labels = [
        'postal' => 'Envoi postal',
        'hand' => 'Remise en main propre',
        'both' => 'Envoi postal ou remise en main propre'
    ];
    return $labels[$type] ?? $type;
}

/**
 * Validate file upload (image)
 * @param array $file $_FILES['field']
 * @param int $max_size Max size in bytes (default 200KB)
 * @return array ['success' => bool, 'message' => string]
 */
function validateImageUpload($file, $max_size = 204800) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erreur lors du téléchargement.'];
    }

    // Check MIME type
    $mime = mime_content_type($file['tmp_name']);
    if (!in_array($mime, ['image/jpeg', 'image/jpg'])) {
        return ['success' => false, 'message' => 'Seules les images JPEG sont acceptées.'];
    }

    // Check file size
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'La taille du fichier dépasse 200 KB.'];
    }

    return ['success' => true, 'message' => 'Validation réussie.'];
}

/**
 * Generate unique filename for uploaded file
 * @param string $original_name
 * @return string
 */
function generateUniqueFilename($original_name) {
    $ext = pathinfo($original_name, PATHINFO_EXTENSION);
    return bin2hex(random_bytes(16)) . '.' . strtolower($ext);
}
