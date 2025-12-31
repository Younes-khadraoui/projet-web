<?php

class AdminController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all ads with seller info for admin view
     */
    public function getAllAds() {
        $stmt = $this->db->prepare(
            'SELECT a.*, 
                    u.name as seller_name, u.email as seller_email,
                    c.name as category_name
             FROM ads a
             LEFT JOIN users u ON a.seller_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             ORDER BY a.created_at DESC'
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get all users with their ad counts
     */
    public function getAllUsers() {
        $stmt = $this->db->prepare(
            'SELECT u.id, u.name, u.email, u.role, u.created_at,
                    COUNT(DISTINCT a.id) as ad_count,
                    COUNT(DISTINCT CASE WHEN a.is_sold = 1 THEN a.id END) as sold_count
             FROM users u
             LEFT JOIN ads a ON u.id = a.seller_id
             GROUP BY u.id
             ORDER BY u.created_at DESC'
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete an ad by ID
     */
    public function deleteAd($ad_id) {
        try {
            $stmt = $this->db->prepare('DELETE FROM ads WHERE id = ?');
            $stmt->execute([$ad_id]);
            return ['success' => true, 'message' => 'Annonce supprimée.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Delete a user and all their ads
     */
    public function deleteUser($user_id) {
        try {
            // Don't allow deletion of admin user if it's the last admin
            $stmt = $this->db->prepare(
                'SELECT role FROM users WHERE id = ?'
            );
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user['role'] === 'admin') {
                // Check if there are other admins
                $stmt = $this->db->prepare(
                    'SELECT COUNT(*) as count FROM users WHERE role = "admin"'
                );
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($result['count'] <= 1) {
                    return ['success' => false, 'message' => 'Impossible de supprimer le dernier administrateur.'];
                }
            }

            // Delete all ads by this user first
            $stmt = $this->db->prepare('DELETE FROM ads WHERE seller_id = ?');
            $stmt->execute([$user_id]);

            // Delete the user
            $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
            $stmt->execute([$user_id]);

            return ['success' => true, 'message' => 'Utilisateur et ses annonces supprimés.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Add a new category
     */
    public function addCategory($name) {
        $name = trim($name);
        if (empty($name) || strlen($name) < 2) {
            return ['success' => false, 'message' => 'Le nom doit contenir au minimum 2 caractères.'];
        }

        try {
            $stmt = $this->db->prepare('INSERT INTO categories (name) VALUES (?)');
            $stmt->execute([$name]);
            return ['success' => true, 'message' => 'Catégorie créée avec succès.'];
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                return ['success' => false, 'message' => 'Cette catégorie existe déjà.'];
            }
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Rename a category
     */
    public function renameCategory($category_id, $new_name) {
        $new_name = trim($new_name);
        if (empty($new_name) || strlen($new_name) < 2) {
            return ['success' => false, 'message' => 'Le nom doit contenir au minimum 2 caractères.'];
        }

        try {
            $stmt = $this->db->prepare('UPDATE categories SET name = ? WHERE id = ?');
            $stmt->execute([$new_name, $category_id]);
            return ['success' => true, 'message' => 'Catégorie renommée avec succès.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        $stmt = $this->db->prepare(
            'SELECT c.id, c.name, COUNT(a.id) as ad_count
             FROM categories c
             LEFT JOIN ads a ON c.id = a.category_id
             GROUP BY c.id
             ORDER BY c.name ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete a category and all its ads
     */
    public function deleteCategory($category_id) {
        try {
            // Delete all ads in this category first
            $stmt = $this->db->prepare('DELETE FROM ads WHERE category_id = ?');
            $stmt->execute([$category_id]);

            // Delete the category
            $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$category_id]);

            return ['success' => true, 'message' => 'Catégorie et ses annonces supprimées.'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
}
