<?php

class CategoryController {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Get all categories with ad counts
     * @return array
     */
    public function getAllWithCounts() {
        $stmt = $this->db->prepare(
            'SELECT c.id, c.name, COUNT(a.id) as ad_count
             FROM categories c
             LEFT JOIN ads a ON c.id = a.category_id AND a.is_sold = 0
             GROUP BY c.id
             ORDER BY c.name ASC'
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get category by ID
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create new category (admin only)
     * @param string $name
     * @return array
     */
    public function create($name) {
        $name = trim($name);
        if (empty($name) || strlen($name) < 2) {
            return ['success' => false, 'message' => 'Le nom doit contenir au minimum 2 caractères.'];
        }

        try {
            $stmt = $this->db->prepare('INSERT INTO categories (name) VALUES (?)');
            $stmt->execute([$name]);
            return ['success' => true, 'message' => 'Catégorie créée avec succès!'];
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                return ['success' => false, 'message' => 'Cette catégorie existe déjà.'];
            }
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Rename category (admin only)
     * @param int $id
     * @param string $new_name
     * @return array
     */
    public function rename($id, $new_name) {
        $new_name = trim($new_name);
        if (empty($new_name) || strlen($new_name) < 2) {
            return ['success' => false, 'message' => 'Le nom doit contenir au minimum 2 caractères.'];
        }

        try {
            $stmt = $this->db->prepare('UPDATE categories SET name = ? WHERE id = ?');
            $stmt->execute([$new_name, $id]);
            return ['success' => true, 'message' => 'Catégorie renommée avec succès!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Delete category (admin only)
     * @param int $id
     * @return array
     */
    public function delete($id) {
        // Check if category has ads
        $stmt = $this->db->prepare('SELECT COUNT(*) as count FROM ads WHERE category_id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            return ['success' => false, 'message' => 'Impossible de supprimer une catégorie contenant des annonces.'];
        }

        try {
            $stmt = $this->db->prepare('DELETE FROM categories WHERE id = ?');
            $stmt->execute([$id]);
            return ['success' => true, 'message' => 'Catégorie supprimée avec succès!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }
}
