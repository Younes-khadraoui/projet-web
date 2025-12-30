<h2>Créer une Annonce</h2>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo escape($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" class="form" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category_id">Catégorie *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">-- Sélectionner une catégorie --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo escape($cat['id']); ?>" 
                            <?php echo isset($_POST['category_id']) && $_POST['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo escape($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">Titre *</label>
                <input type="text" id="title" name="title" minlength="5" maxlength="30" 
                    value="<?php echo isset($_POST['title']) ? escape($_POST['title']) : ''; ?>" required>
                <small>5-30 caractères</small>
            </div>

            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" rows="5" minlength="5" maxlength="200" 
                    required><?php echo isset($_POST['description']) ? escape($_POST['description']) : ''; ?></textarea>
                <small>5-200 caractères</small>
            </div>

            <div class="form-group">
                <label for="price">Prix (€) *</label>
                <input type="number" id="price" name="price" step="0.01" min="0" 
                    value="<?php echo isset($_POST['price']) ? escape($_POST['price']) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label>Modes de Livraison *</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="delivery_type[]" value="retrait" 
                            <?php echo isset($_POST['delivery_type']) && in_array('retrait', $_POST['delivery_type']) ? 'checked' : ''; ?>>
                        Retrait sur place
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="delivery_type[]" value="livraison" 
                            <?php echo isset($_POST['delivery_type']) && in_array('livraison', $_POST['delivery_type']) ? 'checked' : ''; ?>>
                        Livraison possible
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="delivery_type[]" value="mondial" 
                            <?php echo isset($_POST['delivery_type']) && in_array('mondial', $_POST['delivery_type']) ? 'checked' : ''; ?>>
                        Mondial Relay
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="photos">Photos</label>
                <input type="file" id="photos" name="photos[]" multiple accept="image/jpeg" />
                <small>Formats acceptés: JPEG. Taille max par photo: 200 KB. Maximum 5 photos.</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Créer l'Annonce</button>
                <a href="/" class="btn btn-secondary">Annuler</a>
            </div>
        </form>

