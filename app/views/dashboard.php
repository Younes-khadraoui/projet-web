<div class="dashboard">
    <h2>Tableau de Bord</h2>
    
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
                                    <img src="/uploads/<?php echo escape($ad['thumbnail']); ?>" alt="<?php echo escape($ad['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image">Pas de photo</div>
                                <?php endif; ?>
                            </div>
                            <div class="ad-info">
                                <h4><?php echo escape($ad['title']); ?></h4>
                                <p class="price"><?php echo formatPrice($ad['price']); ?></p>
                                <a href="/?action=ad&id=<?php echo $ad['id']; ?>" class="btn btn-primary btn-sm">Voir</a>
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
                                    <img src="/uploads/<?php echo escape($ad['thumbnail']); ?>" alt="<?php echo escape($ad['title']); ?>">>
                                <?php else: ?>
                                    <div class="no-image">Pas de photo</div>
                                <?php endif; ?>
                            </div>
                            <div class="ad-info">
                                <h4><?php echo escape($ad['title']); ?></h4>
                                <p class="price"><?php echo formatPrice($ad['price']); ?></p>
                                <p class="buyer">Acheteur: <?php echo isset($ad['buyer_name']) ? escape($ad['buyer_name']) : 'Anonyme'; ?></p>
                                <a href="/?action=ad&id=<?php echo $ad['id']; ?>" class="btn btn-secondary btn-sm">Voir</a>
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
                                    <img src="/uploads/<?php echo escape($ad['thumbnail']); ?>" alt="<?php echo escape($ad['title']); ?>">
                                <?php else: ?>
                                    <div class="no-image">Pas de photo</div>
                                <?php endif; ?>
                            </div>
                            <div class="ad-info">
                                <h4><?php echo escape($ad['title']); ?></h4>
                                <p class="price"><?php echo formatPrice($ad['price']); ?></p>
                                <p class="seller">Vendeur: <?php echo escape($ad['seller_name']); ?></p>
                                <?php if (!$ad['is_received']): ?>
                                    <button class="btn btn-success btn-sm" onclick="confirmReception(<?php echo $ad['id']; ?>)">Confirmer réception</button>
                                <?php else: ?>
                                    <p class="status">✓ Confirmé</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

function confirmReception(adId) {
    if (confirm('Confirmer la réception de cette annonce?')) {
        window.location.href = '/?action=confirm-reception&id=' + adId;
    }
}
</script>
