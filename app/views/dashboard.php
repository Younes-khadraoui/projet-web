<div class="dashboard">
    <h2>Tableau de Bord</h2>

    <?php if (isset($_SESSION['topup_message'])): ?>
        <div class="alert alert-<?= $_SESSION['topup_success'] ? 'success' : 'danger' ?>">
            <?= escape($_SESSION['topup_message']) ?>
        </div>
        <?php unset($_SESSION['topup_message'], $_SESSION['topup_success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['purchase_message'])): ?>
        <div class="alert alert-success">
            <?= escape($_SESSION['purchase_message']) ?>
        </div>
        <?php unset($_SESSION['purchase_message']); ?>
    <?php endif; ?>
    
    <?php if (isLoggedIn()): ?>
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('for-sale')">Mes Annonces</button>
            <button class="tab-btn" onclick="switchTab('sold')">Vendues</button>
            <button class="tab-btn" onclick="switchTab('purchased')">Mes Achats</button>
        </div>

        <!-- Tab: For Sale -->
        <div id="for-sale" class="tab-content active">
            <h3>Annonces en Vente</h3>
            <?php if (empty($for_sale_ads)): ?>
                <p><em>Vous n'avez pas d'annonces en vente.</em></p>
                <a href="/?action=create-ad" class="btn btn-primary">Créer une Annonce</a>
            <?php else: ?>
                <div class="ads-grid">
                    <?php foreach ($for_sale_ads as $ad): ?>
                        <div class="ad-card">
                            <div class="ad-thumbnail">
                                <?php if (!empty($ad['thumbnail'])): ?>
                                    <img src="public/uploads/<?php echo escape($ad['thumbnail']); ?>" alt="<?php echo escape($ad['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image">Pas de photo</div>
                                <?php endif; ?>
                            </div>
                            <div class="ad-info">
                                <h4><?php echo escape($ad['title']); ?></h4>
                                <p class="price"><?php echo formatPrice($ad['price']); ?></p>
                                <a href="<?= $base_url ?>/?action=ad&id=<?php echo $ad['id']; ?>" class="btn btn-primary btn-sm">Voir l'annonce</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab: Sold -->
        <div id="sold" class="tab-content">
            <h3>Annonces Vendues</h3>
            <?php if (empty($sold_ads)): ?>
                <p><em>Vous n'avez pas encore vendu d'annonces.</em></p>
            <?php else: ?>
                <div class="ads-grid">
                    <?php foreach ($sold_ads as $ad): ?>
                        <div class="ad-card">
                            <div class="ad-thumbnail">
                                <?php if (!empty($ad['thumbnail'])): ?>
                                    <img src="public/uploads/<?php echo escape($ad['thumbnail']); ?>" alt="<?php echo escape($ad['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image">Pas de photo</div>
                                <?php endif; ?>
                            </div>
                            <div class="ad-info">
                                <h4><?php echo escape($ad['title']); ?></h4>
                                <p class="price"><?php echo formatPrice($ad['price']); ?></p>
                                <p class="buyer">Acheteur: <?php echo isset($ad['buyer_name']) ? escape($ad['buyer_name']) : 'Anonyme'; ?></p>
                                <a href="<?= $base_url ?>/?action=ad&id=<?php echo $ad['id']; ?>" class="btn btn-secondary btn-sm">Voir l'annonce</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab: Purchased -->
        <div id="purchased" class="tab-content">
            <h3>Mes Achats</h3>
            <?php if (empty($purchased_ads)): ?>
                <p><em>Vous n'avez pas encore acheté d'annonces.</em></p>
            <?php else: ?>
                <div class="ads-grid">
                    <?php foreach ($purchased_ads as $ad): ?>
                        <div class="ad-card">
                            <div class="ad-thumbnail">
                                <?php if (!empty($ad['thumbnail'])): ?>
                                    <img src="public/uploads/<?php echo escape($ad['thumbnail']); ?>" alt="<?php echo escape($ad['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image">Pas de photo</div>
                                <?php endif; ?>
                            </div>
                            <div class="ad-info">
                                <h4><?php echo escape($ad['title']); ?></h4>
                                <p class="price"><?php echo formatPrice($ad['price']); ?></p>
                                <p class="seller">Vendeur: <?php echo escape($ad['seller_name']); ?></p>
                                <?php if (!$ad['is_received']): ?>
                                    <button class="btn btn-success btn-sm" onclick="openReceiptModal(<?php echo $ad['id']; ?>)">Confirmer réception</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>✓ Reçu</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Receipt Confirmation Modal -->
        <div id="receiptModal" class="modal">
            <div class="modal-content">
                <span class="modal-close" onclick="closeReceiptModal()">&times;</span>
                <h2>Confirmer la réception</h2>
                
                <p>Êtes-vous sûr d'avoir reçu cet article ? Cette action est irréversible.</p>
                
                <form method="POST" action="?action=confirm-receipt">
                    <input type="hidden" name="ad_id" id="receiptAdIdInput" value="">
                    
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-success">Confirmer</button>
                        <button type="button" class="btn btn-secondary" onclick="closeReceiptModal()">Annuler</button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p>Veuillez vous connecter pour accéder à votre tableau de bord.</p>
        <a href="/?action=login" class="btn btn-primary">Se connecter</a>
    <?php endif; ?>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    var tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(function(tab) {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    var tabBtns = document.querySelectorAll('.tab-btn');
    tabBtns.forEach(function(btn) {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName).classList.add('active');
    event.target.classList.add('active');
}

function openReceiptModal(adId) {
    document.getElementById('receiptAdIdInput').value = adId;
    document.getElementById('receiptModal').classList.add('show');
}

function closeReceiptModal() {
    document.getElementById('receiptModal').classList.remove('show');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('receiptModal');
    if (event.target === modal) {
        modal.classList.remove('show');
    }
}
</script>
