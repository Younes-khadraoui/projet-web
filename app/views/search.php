<div class="search-results">
    <h1>Résultats de recherche pour "<?= htmlspecialchars($search_query) ?>"</h1>

    <?php if ($total_count === 0): ?>
        <p>Aucune annonce trouvée pour votre recherche.</p>
    <?php else: ?>
        <p>Nombre d'annonces trouvées : <?= $total_count ?></p>
        
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
                        <p class="price"><?php echo formatPrice($ad['price']); ?></p>
                        <a href="<?= $base_url ?>/?action=ad&id=<?php echo $ad['id']; ?>" class="btn btn-primary btn-sm">Voir l'annonce</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="/?action=search&q=<?= htmlspecialchars($search_query) ?>&page=<?= $page - 1 ?>" class="btn btn-secondary">Précédent</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="/?action=search&q=<?= htmlspecialchars($search_query) ?>&page=<?= $i ?>" 
                       class="btn <?= ($i === $page) ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="/?action=search&q=<?= htmlspecialchars($search_query) ?>&page=<?= $page + 1 ?>" class="btn btn-secondary">Suivant</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
