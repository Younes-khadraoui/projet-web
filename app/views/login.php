<div class="auth-container">
    <h2>Connexion</h2>

    <?php if (!empty($auth_data['message'])): ?>
        <div class="alert alert-<?php echo $auth_data['success'] ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($auth_data['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="?action=login" class="auth-form">
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

        <button type="submit" class="btn btn-primary">Se connecter</button>
    </form>

    <p>Pas encore inscrit? <a href="?action=register">S'inscrire</a></p>
</div>
