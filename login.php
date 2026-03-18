<?php
$savedEmail = isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : '';
$loginError = isset($_GET['error']) && $_GET['error'] === '1';
?>

<div class="login-container">
    <h1 class="Titre">Connexion</h1>

    <?php if ($loginError): ?>
        <p class="login-error">Email ou mot de passe incorrect.</p>
    <?php endif; ?>

    <?php if ($savedEmail !== ''): ?>
        <p class="login-status">Derniere connexion: <?php echo $savedEmail; ?></p>
    <?php endif; ?>

    <form class="login-form" method="POST" action="auth.php">
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?php echo $savedEmail; ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn-login">Se connecter</button>
    </form>
</div>