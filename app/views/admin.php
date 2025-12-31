<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
    <h1>Tableau de bord administrateur</h1>

    <?php if (isset($_SESSION['admin_message'])): ?>
        <div class="alert alert-<?= $_SESSION['admin_message_type'] === 'success' ? 'success' : 'danger' ?>">
            <?= escape($_SESSION['admin_message']) ?>
        </div>
        <?php unset($_SESSION['admin_message'], $_SESSION['admin_message_type']); ?>
    <?php endif; ?>

    <div class="admin-tabs">
        <button class="tab-btn active" onclick="switchAdminTab(event, 'ads')">Gérer les annonces</button>
        <button class="tab-btn" onclick="switchAdminTab(event, 'users')">Gérer les utilisateurs</button>
        <button class="tab-btn" onclick="switchAdminTab(event, 'categories')">Gérer les catégories</button>
    </div>

    <!-- Manage Ads Tab -->
    <div id="ads" class="admin-tab-content active">
        <h2>Toutes les annonces</h2>
        <?php if (empty($ads)): ?>
            <p style="color: var(--secondary);">Aucune annonce trouvée.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Titre</th>
                        <th>Vendeur</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Statut</th>
                        <th>Créée</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ads as $ad): ?>
                        <tr>
                            <td><?= $ad['id'] ?></td>
                            <td><?= escape($ad['title']) ?></td>
                            <td>
                                <?= escape($ad['seller_name'] ?? 'Supprimé') ?><br>
                                <small><?= escape($ad['seller_email'] ?? '') ?></small>
                            </td>
                            <td><?= escape($ad['category_name'] ?? 'N/A') ?></td>
                            <td><?= formatPrice($ad['price']) ?></td>
                            <td>
                                <?php if ($ad['is_sold']): ?>
                                    <span style="color: var(--danger); font-weight: bold;">VENDU</span>
                                <?php else: ?>
                                    <span style="color: var(--success);">Actif</span>
                                <?php endif; ?>
                            </td>
                            <td><?= formatDate($ad['created_at']) ?></td>
                            <td>
                                <button class="btn-danger btn-sm" onclick="openAdminDeleteAdModal(<?= $ad['id'] ?>, '<?= escape(addslashes($ad['title'])) ?>')">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Manage Users Tab -->
    <div id="users" class="admin-tab-content">
        <h2>Tous les utilisateurs</h2>
        <?php if (empty($users)): ?>
            <p style="color: var(--secondary);">Aucun utilisateur trouvé.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Annonces</th>
                        <th>Vendues</th>
                        <th>Inscrit</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= escape($user['name']) ?></td>
                            <td><?= escape($user['email']) ?></td>
                            <td>
                                <span class="badge <?= $user['role'] === 'admin' ? 'badge-danger' : 'badge-secondary' ?>">
                                    <?= $user['role'] === 'admin' ? 'Admin' : 'Utilisateur' ?>
                                </span>
                            </td>
                            <td><?= $user['ad_count'] ?></td>
                            <td><?= $user['sold_count'] ?></td>
                            <td><?= formatDate($user['created_at']) ?></td>
                            <td>
                                <?php 
                                    $adminCount = 0;
                                    foreach ($users as $u) {
                                        if ($u['role'] === 'admin') $adminCount++;
                                    }
                                    $canDelete = !($user['role'] === 'admin' && $adminCount === 1);
                                ?>
                                <?php if ($canDelete): ?>
                                    <button class="btn-danger btn-sm" onclick="openAdminDeleteUserModal(<?= $user['id'] ?>, '<?= escape(addslashes($user['name'])) ?>', <?= $user['ad_count'] ?>)">Supprimer</button>
                                <?php else: ?>
                                    <button class="btn-danger btn-sm" disabled title="Impossible de supprimer le dernier admin">Supprimer</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Manage Categories Tab -->
    <div id="categories" class="admin-tab-content">
        <h2>Catégories</h2>
        
        <div class="admin-form">
            <h3>Ajouter une nouvelle catégorie</h3>
            <form method="POST" action="?action=admin-add-category">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Nom de la catégorie" required minlength="2" maxlength="50">
                </div>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </form>
        </div>

        <?php if (empty($categories)): ?>
            <p style="color: var(--secondary);">Aucune catégorie trouvée.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Annonces</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= escape($cat['name']) ?></td>
                            <td><?= $cat['ad_count'] ?></td>
                            <td>
                                <button class="btn-secondary btn-sm" onclick="openAdminRenameCategoryModal(<?= $cat['id'] ?>, '<?= escape(addslashes($cat['name'])) ?>')">Renommer</button>
                                <button class="btn-danger btn-sm" onclick="openAdminDeleteCategoryModal(<?= $cat['id'] ?>, '<?= escape(addslashes($cat['name'])) ?>', <?= $cat['ad_count'] ?>)">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Ad Modal -->
<div id="adminDeleteAdModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('adminDeleteAdModal')">&times;</span>
        <h2>Confirmer la suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer cette annonce ?<br><strong><span id="deleteAdTitle"></span></strong></p>
        <form method="POST" action="?action=admin-delete-ad">
            <input type="hidden" id="deleteAdId" name="ad_id">
            <div class="form-group" style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('adminDeleteAdModal')">Annuler</button>
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete User Modal -->
<div id="adminDeleteUserModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('adminDeleteUserModal')">&times;</span>
        <h2>Confirmer la suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?<br><strong><span id="deleteUserName"></span></strong></p>
        <p id="deleteUserWarning" style="color: var(--danger); font-size: 0.9em; margin-top: 10px;"></p>
        <form method="POST" action="?action=admin-delete-user">
            <input type="hidden" id="deleteUserId" name="user_id">
            <div class="form-group" style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('adminDeleteUserModal')">Annuler</button>
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </div>
        </form>
    </div>
</div>

<!-- Rename Category Modal -->
<div id="adminRenameCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('adminRenameCategoryModal')">&times;</span>
        <h2>Renommer la catégorie</h2>
        <form method="POST" action="?action=admin-rename-category">
            <input type="hidden" id="renameCategoryId" name="category_id">
            <div class="form-group">
                <input type="text" id="renameCategoryName" name="name" placeholder="Nouveau nom" required minlength="2" maxlength="50">
            </div>
            <div class="form-group" style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('adminRenameCategoryModal')">Annuler</button>
                <button type="submit" class="btn btn-primary">Renommer</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Category Modal -->
<div id="adminDeleteCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('adminDeleteCategoryModal')">&times;</span>
        <h2>Confirmer la suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer cette catégorie ?<br><strong><span id="deleteCategoryName"></span></strong></p>
        <p id="deleteCategoryWarning" style="color: var(--danger); font-size: 0.9em; margin-top: 10px;"></p>
        <form method="POST" action="?action=admin-delete-category">
            <input type="hidden" id="deleteCategoryId" name="category_id">
            <div class="form-group" style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('adminDeleteCategoryModal')">Annuler</button>
                <button type="submit" class="btn btn-danger">Supprimer</button>
            </div>
        </form>
    </div>
</div>

<script>
// Initialize tabs on page load
document.addEventListener('DOMContentLoaded', function() {
    const hash = window.location.hash.substring(1) || 'ads';
    activateTab(hash);
    
    // Auto-hide alert messages after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideUp 0.3s ease forwards';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Listen for hash changes
window.addEventListener('hashchange', function() {
    const hash = window.location.hash.substring(1) || 'ads';
    activateTab(hash);
});

function activateTab(tabName) {
    // Hide all tab contents
    const contents = document.querySelectorAll('.admin-tab-content');
    contents.forEach(c => c.classList.remove('active'));
    
    // Remove active class from all buttons
    const buttons = document.querySelectorAll('.tab-btn');
    buttons.forEach(b => b.classList.remove('active'));
    
    // Show the selected tab and mark button as active
    const tabContent = document.getElementById(tabName);
    if (tabContent) {
        tabContent.classList.add('active');
        
        // Find and activate the corresponding button
        buttons.forEach(btn => {
            if (btn.getAttribute('onclick').includes(`'${tabName}'`)) {
                btn.classList.add('active');
            }
        });
    }
}

function switchAdminTab(evt, tabName) {
    evt.preventDefault();
    window.location.hash = tabName;
}

function openAdminDeleteAdModal(adId, title) {
    document.getElementById('deleteAdId').value = adId;
    document.getElementById('deleteAdTitle').textContent = title;
    document.getElementById('adminDeleteAdModal').classList.add('show');
}

function openAdminDeleteUserModal(userId, name, adCount) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('deleteUserName').textContent = name;
    
    const warning = document.getElementById('deleteUserWarning');
    if (adCount > 0) {
        warning.textContent = `⚠️ Cet utilisateur a ${adCount} annonce(s) qui seront également supprimées.`;
    } else {
        warning.textContent = '';
    }
    
    document.getElementById('adminDeleteUserModal').classList.add('show');
}

function openAdminRenameCategoryModal(categoryId, name) {
    document.getElementById('renameCategoryId').value = categoryId;
    document.getElementById('renameCategoryName').value = name;
    document.getElementById('adminRenameCategoryModal').classList.add('show');
}

function openAdminDeleteCategoryModal(categoryId, name, adCount) {
    document.getElementById('deleteCategoryId').value = categoryId;
    document.getElementById('deleteCategoryName').textContent = name;
    
    const warning = document.getElementById('deleteCategoryWarning');
    if (adCount > 0) {
        warning.textContent = `⚠️ Cette catégorie contient ${adCount} annonce(s) qui seront également supprimées.`;
    } else {
        warning.textContent = '';
    }
    
    document.getElementById('adminDeleteCategoryModal').classList.add('show');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('show');
    }
}
</script>

<style>
.admin-tabs {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
    border-bottom: 2px solid var(--light);
}

.tab-btn {
    padding: 12px 20px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 1em;
    color: var(--secondary);
    font-weight: 500;
    transition: all 0.3s ease;
}

.tab-btn:hover {
    color: var(--primary);
}

.tab-btn.active {
    color: var(--primary);
    border-bottom-color: var(--primary);
}

.admin-tab-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.admin-tab-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.admin-table thead {
    background: var(--light);
}

.admin-table th {
    padding: 15px;
    text-align: left;
    font-weight: 600;
    color: var(--dark);
    border-bottom: 2px solid #ddd;
}

.admin-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.admin-table tbody tr:hover {
    background: #f9f9f9;
}

.admin-form {
    background: var(--light);
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.admin-form h3 {
    margin-top: 0;
    color: var(--dark);
}

.admin-form form {
    display: flex;
    gap: 10px;
    align-items: flex-end;
}

.admin-form .form-group {
    flex: 1;
    margin: 0;
}

.admin-form input {
    width: 100%;
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.85em;
    font-weight: 600;
}

.badge-danger {
    background: #ffe6e6;
    color: var(--danger);
}

.badge-secondary {
    background: #f0f0f0;
    color: var(--secondary);
}

.btn-sm {
    padding: 6px 12px;
    font-size: 0.9em;
}
</style>
