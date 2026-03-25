<?php
$registerError = isset($_GET['error']) ? $_GET['error'] : '';
$registerSuccess = isset($_GET['success']) && $_GET['success'] === '1';
?>

<div class="login-container">
    <h1 class="Titre">Inscription</h1>

    <?php if ($registerError === 'missing'): ?>
        <p class="login-error">Tous les champs sont obligatoires.</p>
    <?php elseif ($registerError === 'invalid_email'): ?>
        <p class="login-error">Le login doit etre un email valide.</p>
    <?php elseif ($registerError === 'password_short'): ?>
        <p class="login-error">Le mot de passe doit contenir au moins 6 caracteres.</p>
    <?php elseif ($registerError === 'exists'): ?>
        <p class="login-error">Ce login ou ce pseudo existe deja.</p>
    <?php elseif ($registerError === 'schema_mdp'): ?>
        <p class="login-error">La colonne parieur_mdp est trop courte pour SHA1. Executez: ALTER TABLE parieur MODIFY parieur_mdp VARCHAR(40) NOT NULL;</p>
    <?php elseif ($registerError === 'db'): ?>
        <p class="login-error">Erreur base de donnees, veuillez reessayer.</p>
    <?php endif; ?>

    <?php if ($registerSuccess): ?>
        <p class="login-status">Inscription reussie. Vous pouvez maintenant vous connecter.</p>
    <?php endif; ?>

    <form class="login-form" method="POST" action="register_auth.php">
        <div class="form-group">
            <label for="email">Login (email) :</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" maxlength="100" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" minlength="6" required>
        </div>

        <button type="submit" class="btn-login">S'inscrire</button>
    </form>

    <p class="auth-switch-link">Deja un compte ? <a href="Index.php?page=login">Se connecter</a></p>
</div>
