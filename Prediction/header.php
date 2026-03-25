<!-- Header reutilisable : a inclure dans les pages avec include 'header.php' -->
<!-- Le CSS est charge ici pour conserver la meme apparence -->
<link rel="stylesheet" href="styles.css">

<header>
    <nav class="menu">
        <ul>
            <li><a href="?page=accueil">Accueil</a></li>
            <li><a href="?page=profil">Profil</a></li>
            <?php if (isset($_COOKIE['is_logged_in']) && $_COOKIE['is_logged_in'] === '1'): ?>
                <li><a href="?logout=1">Deconnexion</a></li>
            <?php else: ?>
                <li><a href="?page=login">Login</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>