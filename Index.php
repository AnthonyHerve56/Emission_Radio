
<!DOCTYPE html>
<html lang="fr">
<?php
    include 'fonction.php';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Anthony HERVE , Mohamed Ali SHAIEK">
    <title>Accueil</title>
    <link rel="stylesheet" href="./style.css/styles.css">
</head>

<body>
    <?php include 'header.php'; ?>
    <h1 class="Titre">Bienvenue sur notre site de prédiction de match de football !</h1>

    <div class="accueil-container">
        <div class="navigation">
            <p>Explorez les différentes sections pour découvrir les dernières tendances et analyses pour vos équipes
                préférées.</p>
                
        </div>
        <div class="Affichage_Principal">
            <p class="rainbow-text">Découvrez les dernières tendances et analyses pour vos équipes préférées.</p>
            <?php afficherMatch(); ?>
        </div>
    </div>
    <?php include 'footer.php'; ?>
    
</body>


</html>