<?php
    include 'fonction.php';
    $equipe_1= htmlspecialchars(urldecode($_GET['equipe1']));
    $equipe_2= htmlspecialchars(urldecode($_GET['equipe2']));     

?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Anthony HERVE , Mohamed Ali SHAIEK">
    <title>Accueil</title>
    <link rel="stylesheet" href="./style.css/styles.css">
</head>

<body>
    <header>
        <nav class="menu">
            <ul>
            <li><a href="Index.php">Accueil</a></li>
                <li><a href="Profil.html">Profil</a></li>
            </ul>
        </nav>
    </header>
    <h1 class="Titre">Match : <?php echo $equipe_1; ?> vs <?php echo $equipe_2; ?></h1>
    <div class="accueil-container">
        
    </div>
        
    <footer id="Contact">
        <p> Tous droits réservés.</p>
        <p>Contactez-nous : <a href="mailto:contact@predictionmatch.fr">contact@predictionmatch.fr</a></p>
        <p>Autheur de ce site : Anthony HERVE et Mohamed Ali SHAIEK</p>
    </footer>
</body>


</html>