<?php $isLoggedIn = isset($_COOKIE['is_logged_in']) && $_COOKIE['is_logged_in'] === '1';
if (!$isLoggedIn) {
    echo "<div class='profil-container'>
    <h1 class='Titre'>Profil</h1>
    <p class='profil-info'>Veuillez vous connecter.</p>
    </div>";
    return;
}
$pseudo = isset($_COOKIE['user_pseudo']) ? htmlspecialchars($_COOKIE['user_pseudo']) : 'Inconnu';
$email = isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : 'Inconnu'; ?>
<div class="profil-container">
<h1 class="Titre">Profil de l'utilisateur</h1>
<div class="profil-info">
    
    <p>Nom : <?php echo $pseudo; ?></p>
    <p>Email : <?php echo $email; ?></p>
</div>
</div>