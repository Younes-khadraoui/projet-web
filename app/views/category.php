<div class="category-view">
    <h2><?php echo escape($category['name']); ?></h2>
    <p class="category-description"><?php echo $total_count; ?> annonce<?php echo $total_count !== 1 ? 's' : ''; ?></p>

    <?php if (empty($ads)): ?>
        <p><em>Aucune annonce dans cette catégorie pour le moment.</em></p>
    <?php else: ?>
        <div class="ads-grid">
            <?php foreach ($ads as $ad): ?>
            <div class="ad-card <?= $ad['is_sold'] ? 'sold' : '' ?>">
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
        </div>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?action=category&id=<?php echo $category_id; ?>&page=<?php echo $page - 1; ?>" class="btn btn-secondary">
                    ← Précédent
                </a>
            <?php endif; ?>

            <span class="page-info">Page <?php echo $page; ?> sur <?php echo $total_pages; ?></span>

            <?php if ($page < $total_pages): ?>
                <a href="?action=category&id=<?php echo $category_id; ?>&page=<?php echo $page + 1; ?>" class="btn btn-secondary">
                    Suivant →
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
