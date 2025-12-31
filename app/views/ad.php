<div class="ad-detail">
    <?php if (isset($_SESSION['purchase_error'])): ?>
        <div class="alert alert-danger">
            <?= escape($_SESSION['purchase_error']) ?>
        </div>
        <?php unset($_SESSION['purchase_error']); ?>
    <?php endif; ?>

    <?php if ($ad): ?>
        <div class="ad-detail-container">
            <!-- Photos Section -->
            <div class="ad-photos">
                <?php if (!empty($photos)): ?>
                    <div class="photo-gallery">
                        <div class="main-photo">
                            <img id="mainPhoto" src="/uploads/<?php echo escape($photos[0]['filename']); ?>" alt="<?php echo escape($ad['title']); ?>">
                        </div>
                        <?php if (count($photos) > 1): ?>
                            <div class="thumbnail-list">
                                <?php foreach ($photos as $index => $photo): ?>
                                    <img class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" 
                                         src="/uploads/<?php echo escape($photo['filename']); ?>" 
                                         alt="Photo <?php echo $index + 1; ?>"
                                         onclick="changePhoto('/uploads/<?php echo escape($photo['filename']); ?>')">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="no-image-large">Pas de photo disponible</div>
                <?php endif; ?>
            </div>

            <!-- Details Section -->
            <div class="ad-details">
                <div class="ad-header">
                    <h2><?php echo escape($ad['title']); ?></h2>
                    <?php if ($ad['is_sold']): ?>
                        <span class="sold-badge">Vendu</span>
                    <?php endif; ?>
                </div>
                
                <div class="ad-meta">
                    <p class="seller"><strong>Vendeur:</strong> <?php echo escape($ad['seller_name']); ?></p>
                    <p class="posted-date"><strong>Publié le:</strong> <?php echo formatDate($ad['created_at']); ?></p>
                </div>

                <div class="ad-price">
                    <p class="price-label">Prix:</p>
                    <p class="price-value"><?php echo formatPrice($ad['price']); ?></p>
                </div>

                <div class="ad-category">
                    <p><strong>Catégorie:</strong> <?php echo escape($ad['category_name']); ?></p>
                </div>

                <div class="ad-description">
                    <h3>Description</h3>
                    <p><?php echo nl2br(escape($ad['description'])); ?></p>
                </div>

                <div class="ad-delivery">
                    <h3>Modes de Livraison</h3>
                    <ul>
                        <?php 
                        $delivery_modes = explode(',', $ad['delivery_type']);
                        $delivery_labels = [
                            'retrait' => 'Retrait sur place',
                            'livraison' => 'Livraison possible',
                            'mondial' => 'Mondial Relay'
                        ];
                        ?>
                        <?php foreach ($delivery_modes as $mode): ?>
                            <li><?php echo $delivery_labels[trim($mode)] ?? htmlspecialchars(trim($mode)); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="ad-status">
                    <?php if ($ad['is_sold']): ?>
                        <p class="status-sold"><strong>Statut:</strong> Vendu</p>
                    <?php else: ?>
                        <p class="status-available"><strong>Statut:</strong> Disponible</p>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="ad-actions">
                    <?php 
                    $is_owner = isLoggedIn() && $ad['seller_id'] == $_SESSION['user_id'];
                    $can_buy = isLoggedIn() && !$is_owner && !$ad['is_sold'];
                    ?>
                    
                    <?php if ($is_owner): ?>
                        <!-- Owner Actions -->
                        <button class="btn btn-danger" onclick="openDeleteModal(<?php echo $ad['id']; ?>)">
                            Supprimer l'annonce
                        </button>
                    <?php elseif ($can_buy): ?>
                        <!-- Buy Actions -->
                        <button class="btn btn-primary btn-buy" onclick="openBuyModal(<?php echo $ad['id']; ?>, '<?php echo implode(',', array_map('trim', explode(',', $ad['delivery_type']))); ?>')">
                            Acheter
                        </button>
                    <?php elseif ($ad['is_sold']): ?>
                        <button class="btn btn-secondary" disabled>
                            Cet article a été vendu
                        </button>
                    <?php else: ?>
                        <button class="btn btn-primary" onclick="location.href='?action=login'">
                            Connexion requise
                        </button>
                    <?php endif; ?>
                    
                    <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
                </div>
            </div>
        </div>

        <!-- Buy Modal -->
        <div id="buyModal" class="modal">
            <div class="modal-content">
                <span class="modal-close" onclick="closeBuyModal()">&times;</span>
                <h2>Confirmer l'achat</h2>
                
                <form method="POST" action="?action=buy">
                    <input type="hidden" name="ad_id" id="adIdInput" value="">
                    
                    <div class="form-group">
                        <label for="delivery">Mode de livraison *</label>
                        <select id="delivery" name="delivery_type" required>
                            <option value="">-- Sélectionner un mode --</option>
                            <option value="retrait">Retrait sur place</option>
                            <option value="livraison">Livraison possible</option>
                            <option value="mondial">Mondial Relay</option>
                        </select>
                    </div>

                    <div class="modal-actions">
                        <button type="submit" class="btn btn-primary">Confirmer l'achat</button>
                        <button type="button" class="btn btn-secondary" onclick="closeBuyModal()">Annuler</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div id="deleteModal" class="modal">
            <div class="modal-content">
                <span class="modal-close" onclick="closeDeleteModal()">&times;</span>
                <h2>Supprimer l'annonce</h2>
                
                <p>Êtes-vous sûr de vouloir supprimer cette annonce ? Cette action est irréversible.</p>
                
                <form method="POST" action="?action=delete-ad">
                    <input type="hidden" name="ad_id" id="deleteAdIdInput" value="">
                    
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Annuler</button>
                    </div>
                </form>
            </div>
        </div>

    <?php else: ?>
        <div class="error-message">
            <p><em>Annonce non trouvée.</em></p>
            <a href="/" class="btn btn-secondary">Retour à l'accueil</a>
        </div>
    <?php endif; ?>
</div>

<script>
function changePhoto(photoUrl) {
    document.getElementById('mainPhoto').src = photoUrl;
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.classList.remove('active');
        if (thumb.src.endsWith(photoUrl.split('/').pop())) {
            thumb.classList.add('active');
        }
    });
}

function openBuyModal(adId, deliveryModes) {
    document.getElementById('adIdInput').value = adId;
    const select = document.getElementById('delivery');
    const modes = deliveryModes.split(',').map(m => m.trim());
    
    // Clear and populate options
    select.innerHTML = '<option value="">-- Sélectionner un mode --</option>';
    const labels = {
        'retrait': 'Retrait sur place',
        'livraison': 'Livraison possible',
        'mondial': 'Mondial Relay'
    };
    
    modes.forEach(mode => {
        if (labels[mode]) {
            const opt = document.createElement('option');
            opt.value = mode;
            opt.textContent = labels[mode];
            select.appendChild(opt);
        }
    });
    
    document.getElementById('buyModal').classList.add('show');
}

function closeBuyModal() {
    document.getElementById('buyModal').classList.remove('show');
}

function openDeleteModal(adId) {
    document.getElementById('deleteAdIdInput').value = adId;
    document.getElementById('deleteModal').classList.add('show');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const buyModal = document.getElementById('buyModal');
    const deleteModal = document.getElementById('deleteModal');
    
    if (event.target === buyModal) {
        buyModal.classList.remove('show');
    }
    if (event.target === deleteModal) {
        deleteModal.classList.remove('show');
    }
}
</script>
