<div class="homepage">
    <?php if (isLoggedIn()): ?>
        <div class="create-ad-section">
            <p>Vous avez quelque chose à vendre?</p>
            <a href="<?= $base_url ?>/?action=create-ad" class="btn btn-primary">Créer une Annonce</a>
        </div>
    <?php endif; ?>

    <h2>Catégories</h2>
    <div class="category-list">
        <?php foreach ($categories as $category): ?>
            <div class="category-card">
                <h3><?php echo escape($category['name']); ?></h3>
                <p class="ad-count"><?php echo $category['ad_count']; ?> annonce<?php echo $category['ad_count'] !== 1 ? 's' : ''; ?></p>
                <?php if ($category['ad_count'] > 0): ?>
                    <a href="?action=category&id=<?php echo $category['id']; ?>" class="btn btn-secondary">
                        Voir les biens
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary" disabled>
                        Voir les biens
                    </button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Derniers biens mis en vente</h2>
    <div class="recent-ads">
        <?php if (empty($recent_ads)): ?>
            <p><em>Aucune annonce pour le moment.</em></p>
        <?php else: ?>
            <?php foreach ($recent_ads as $ad): ?>
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
                        <p class="seller">Par: <?php echo escape($ad['seller_name']); ?></p>
                        <p class="price"><?php echo formatPrice($ad['price']); ?></p>
                        <a href="?action=ad&id=<?php echo $ad['id']; ?>" class="btn btn-primary">
                            Voir l'annonce
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
