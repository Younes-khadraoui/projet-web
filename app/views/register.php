<div class="auth-container">
    <h2>S'inscrire</h2>

    <?php if (!empty($auth_data['message'])): ?>
        <div class="alert alert-<?php echo $auth_data['success'] ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($auth_data['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="?action=register" class="auth-form">
        <div class="form-group">
            <label for="name">Nom:</label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" 
                required 
            >
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?php echo htmlspecialchars($auth_data['email']); ?>" 
                required 
            >
        </div>

        <div class="form-group">
            <label for="password">Mot de passe:</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                required 
            >
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe:</label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                required 
            >
        </div>

        <button type="submit" class="btn btn-primary">S'inscrire</button>
    </form>

    <p>Déjà inscrit? <a href="?action=login">Se connecter</a></p>
</div>
