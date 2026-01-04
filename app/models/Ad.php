<?php

class Ad {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Create a new ad
     * @param int $seller_id
     * @param int $category_id
     * @param string $title (5-30 chars)
     * @param string $description (5-200 chars)
     * @param float $price
     * @param string $delivery_type (comma-separated: retrait,livraison,mondial)
     * @return array ['success' => bool, 'message' => string, 'ad_id' => int|null]
     */
    public function create($seller_id, $category_id, $title, $description, $price, $delivery_type) {
        // Validate title
        $title = trim($title);
        if (strlen($title) < 5 || strlen($title) > 30) {
            return ['success' => false, 'message' => 'Le titre doit contenir entre 5 et 30 caractères.'];
        }

        // Validate description
        $description = trim($description);
        if (strlen($description) < 5 || strlen($description) > 200) {
            return ['success' => false, 'message' => 'La description doit contenir entre 5 et 200 caractères.'];
        }

        // Validate price
        $price = (float) $price;
        if ($price < 0) {
            return ['success' => false, 'message' => 'Le prix ne peut pas être négatif.'];
        }

        // Validate delivery type is not empty
        if (empty($delivery_type)) {
            return ['success' => false, 'message' => 'Au moins un mode de livraison doit être sélectionné.'];
        }

        // Validate category exists
        $stmt = $this->db->prepare('SELECT id FROM categories WHERE id = ?');
        $stmt->execute([$category_id]);
        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Catégorie invalide.'];
        }

        // Insert ad
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO ads (seller_id, category_id, title, description, price, delivery_type, is_sold, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, 0, NOW())'
            );
            $stmt->execute([$seller_id, $category_id, $title, $description, $price, $delivery_type]);
            $ad_id = $this->db->lastInsertId();
            return ['success' => true, 'message' => 'Annonce créée avec succès!', 'ad_id' => $ad_id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur lors de la création: ' . $e->getMessage()];
        }
    }

    /**
     * Get all ads for a category with pagination
     * @param int $category_id
     * @param int $page (1-indexed)
     * @param int $per_page
     * @return array ['ads' => [], 'total' => int, 'pages' => int]
     */
    public function getByCategory($category_id, $page = 1, $per_page = 10) {
        $page = (int)$page;
        $per_page = (int)$per_page;
        $offset = ($page - 1) * $per_page;

        // Get total count
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) as total FROM ads WHERE category_id = ? AND is_sold = 0'
        );
        $stmt->execute([$category_id]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get ads with pagination
        $stmt = $this->db->prepare(
            'SELECT a.*, u.name as seller_name, 
                    (SELECT filename FROM photos WHERE ad_id = a.id AND is_primary = 1 LIMIT 1) as thumbnail
             FROM ads a
             LEFT JOIN users u ON a.seller_id = u.id
             WHERE a.category_id = ? AND a.is_sold = 0
             ORDER BY a.created_at DESC
             LIMIT ' . $per_page . ' OFFSET ' . $offset
        );
        $stmt->execute([$category_id]);
        $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'ads' => $ads,
            'total' => $total,
            'pages' => ceil($total / $per_page)
        ];
    }

    /**
     * Get ad by ID with full details
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $stmt = $this->db->prepare(
            'SELECT a.*, 
                    u.name as seller_name, u.email as seller_email,
                    c.name as category_name
             FROM ads a
             LEFT JOIN users u ON a.seller_id = u.id
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get the 4 most recent ads
     * @return array
     */
    public function getRecent($limit = 4) {
        $limit = (int)$limit;
        $stmt = $this->db->prepare(
            'SELECT a.*, u.name as seller_name,
                    (SELECT filename FROM photos WHERE ad_id = a.id AND is_primary = 1 LIMIT 1) as thumbnail
             FROM ads a
             LEFT JOIN users u ON a.seller_id = u.id
             WHERE a.is_sold = 0
             ORDER BY a.created_at DESC
             LIMIT ' . $limit
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get ads sold by a user
     * @param int $seller_id
     * @return array
     */
    public function getSoldByUser($seller_id) {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.name as buyer_name,
                    (SELECT filename FROM photos WHERE ad_id = a.id AND is_primary = 1 LIMIT 1) as thumbnail
             FROM ads a
             LEFT JOIN users u ON a.buyer_id = u.id
             WHERE a.seller_id = ? AND a.is_sold = 1
             ORDER BY a.created_at DESC'
        );
        $stmt->execute([$seller_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get ads purchased by a user
     * @param int $buyer_id
     * @return array
     */
    public function getPurchasedByUser($buyer_id) {
        $stmt = $this->db->prepare(
            'SELECT a.*, u.name as seller_name,
                    (SELECT filename FROM photos WHERE ad_id = a.id AND is_primary = 1 LIMIT 1) as thumbnail
             FROM ads a
             LEFT JOIN users u ON a.seller_id = u.id
             WHERE a.buyer_id = ?
             ORDER BY a.created_at DESC'
        );
        $stmt->execute([$buyer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get ads for sale by a user
     * @param int $seller_id
     * @return array
     */
    public function getForSaleByUser($seller_id) {
        $stmt = $this->db->prepare(
            'SELECT a.*, c.name as category_name,
                    (SELECT filename FROM photos WHERE ad_id = a.id AND is_primary = 1 LIMIT 1) as thumbnail
             FROM ads a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.seller_id = ? AND a.is_sold = 0
             ORDER BY a.created_at DESC'
        );
        $stmt->execute([$seller_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark ad as sold (purchase)
     * @param int $ad_id
     * @param int $buyer_id
     * @return bool
     */
    public function markAsSold($ad_id, $buyer_id) {
        $stmt = $this->db->prepare(
            'UPDATE ads SET is_sold = 1, buyer_id = ? WHERE id = ?'
        );
        return $stmt->execute([$buyer_id, $ad_id]);
    }

    /**
     * Mark ad as received
     * @param int $ad_id
     * @return bool
     */
    public function markAsReceived($ad_id) {
        $stmt = $this->db->prepare(
            'UPDATE ads SET is_received = 1 WHERE id = ?'
        );
        return $stmt->execute([$ad_id]);
    }

    /**
     * Delete an ad
     * @param int $ad_id
     * @return bool
     */
    public function delete($ad_id) {
        $stmt = $this->db->prepare('DELETE FROM ads WHERE id = ?');
        return $stmt->execute([$ad_id]);
    }

    /**
     * Check if user owns the ad
     * @param int $ad_id
     * @param int $user_id
     * @return bool
     */
    public function isOwner($ad_id, $user_id) {
        $stmt = $this->db->prepare('SELECT id FROM ads WHERE id = ? AND seller_id = ?');
        $stmt->execute([$ad_id, $user_id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Search for ads by title or description
     * @param string $query The search query
     * @param int $page Current page number
     * @param int $per_page Ads per page
     * @return array ['ads' => array, 'total' => int]
     */
    public function search($query, $page, $per_page) {
        $offset = ($page - 1) * $per_page;
        $search_term = '%' . $query . '%';

        // Query for ads
        $stmt = $this->db->prepare(
             'SELECT a.id, a.title, a.price, a.is_sold, 
                    (SELECT p.filename FROM photos p WHERE p.ad_id = a.id ORDER BY p.is_primary DESC, p.id ASC LIMIT 1) as thumbnail
             FROM ads a
             WHERE (a.title LIKE :search_term OR a.description LIKE :search_term) AND a.is_sold = 0
             ORDER BY a.created_at DESC
             LIMIT :offset, :limit'
        );
        $search_param = '%' . $query . '%';
        $stmt->bindParam(':search_term', $search_param, PDO::PARAM_STR);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $per_page, PDO::PARAM_INT);
        $stmt->execute();
        $ads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count for pagination
        $total_stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM ads 
             WHERE (title LIKE ? OR description LIKE ?) AND is_sold = 0'
        );
        $total_stmt->execute([$search_term, $search_term]);
        $total_count = $total_stmt->fetchColumn();

        return ['ads' => $ads, 'total' => $total_count];
    }
}
