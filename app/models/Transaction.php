<?php

class Transaction {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    /**
     * Process a purchase transaction
     * Deduct from buyer, add to seller
     */
    public function processPurchase($ad_id, $buyer_id, $seller_id, $amount) {
        try {
            // Start transaction
            $this->db->beginTransaction();

            $userModel = new User($this->db);

            // Check buyer has sufficient balance
            $buyer = $userModel->getById($buyer_id);
            if (!$buyer || $buyer['balance'] < $amount) {
                return ['success' => false, 'message' => 'Solde insuffisant pour cet achat.'];
            }
            
            // Get seller to update their balance
            $seller = $userModel->getById($seller_id);
            if (!$seller) {
                return ['success' => false, 'message' => 'Vendeur non trouvé.'];
            }

            // Deduct from buyer
            $userModel->updateBalance($buyer_id, $buyer['balance'] - $amount);

            // Add to seller
            $userModel->updateBalance($seller_id, $seller['balance'] + $amount);

            // Record transaction
            $stmt = $this->db->prepare(
                'INSERT INTO transactions (ad_id, buyer_id, seller_id, amount, type, created_at) 
                 VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $stmt->execute([$ad_id, $buyer_id, $seller_id, $amount, 'purchase']);

            $this->db->commit();
            return ['success' => true, 'message' => 'Transaction réussie.'];

        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Top up user balance
     */
    public function topUpBalance($user_id, $amount) {
        if ($amount <= 0) {
            return ['success' => false, 'message' => 'Le montant doit être supérieur à 0.'];
        }

        if ($amount > 10000) {
            return ['success' => false, 'message' => 'Le montant maximum est 10 000 €.'];
        }

        try {
            // Record top-up transaction
            $stmt = $this->db->prepare(
                'INSERT INTO transactions (user_id, amount, type, created_at) 
                 VALUES (?, ?, ?, NOW())'
            );
            $stmt->execute([$user_id, $amount, 'topup']);

            return ['success' => true, 'message' => 'Solde crédité avec succès!'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }

    /**
     * Get user's transaction history
     */
    public function getUserTransactions($user_id, $limit = 20) {
        $stmt = $this->db->prepare(
            'SELECT t.*, 
                    a.title as ad_title,
                    b.name as buyer_name,
                    s.name as seller_name
             FROM transactions t
             LEFT JOIN ads a ON t.ad_id = a.id
             LEFT JOIN users b ON t.buyer_id = b.id
             LEFT JOIN users s ON t.seller_id = s.id
             WHERE t.user_id = ? OR t.buyer_id = ? OR t.seller_id = ?
             ORDER BY t.created_at DESC
             LIMIT ?'
        );
        $stmt->execute([$user_id, $user_id, $user_id, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
