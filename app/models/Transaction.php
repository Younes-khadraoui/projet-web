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

            // Check buyer has sufficient balance
            $stmt = $this->db->prepare('SELECT balance FROM users WHERE id = ?');
            $stmt->execute([$buyer_id]);
            $buyer = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($buyer['balance'] < $amount) {
                return ['success' => false, 'message' => 'Solde insuffisant pour cette achat.'];
            }

            // Deduct from buyer
            $stmt = $this->db->prepare('UPDATE users SET balance = balance - ? WHERE id = ?');
            $stmt->execute([$amount, $buyer_id]);

            // Add to seller
            $stmt = $this->db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
            $stmt->execute([$amount, $seller_id]);

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
            $stmt = $this->db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
            $stmt->execute([$amount, $user_id]);
            
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
