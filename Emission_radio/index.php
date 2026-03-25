<!DOCTYPE html>
<html lang="fr">
<?php
    // Chargement central des fonctions utilitaires (BDD, helpers, etc.).
    include 'fonction.php';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Anthony HERVE , Mohamed Ali SHAIEK">
    <title>Accueil Emission</title>
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
    <!-- Entete commun a toutes les pages -->
    <?php include 'header.php'; ?>

    <?php 
    // Routeur simple base sur le parametre GET "page".
    // Exemple: index.php?page=emission
    if(isset($_GET['page'])){
        if($_GET['page'] == 'login'){
            include 'login.php';
        }
        else if($_GET['page'] == 'emission'){
            include 'Emission.php';
        }
        else if($_GET['page'] == 'emission_form'){
            include 'EmissionForm.php';
        }
        else if($_GET['page'] == 'evenement'){
            include 'Evenement.php';
        }
        else if($_GET['page'] == 'evenement_form'){
            include 'EvenementForm.php';
        }
        else if($_GET['page'] == 'menace'){
            include 'Menace.php';
        }
        else if($_GET['page'] == 'menace_form'){
            include 'MenaceForm.php';
        }
        else if($_GET['page'] == 'victime'){
            include 'Victime.php';
        }
        else if($_GET['page'] == 'victime_form'){
            include 'VictimeForm.php';
        }

        else{
            // Valeur inconnue => retour sur l'accueil.
            include 'Accueil.php'; 
        }
    }
    else{
        // Aucune page demandee => accueil par defaut.
        include 'Accueil.php'; 
    }
    ?>

    <!-- Pied de page commun a toutes les pages -->
    <?php include 'footer.php'; ?>
    
</body>


</html>