<!DOCTYPE html>
<html lang="fr">
<?php
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
    <?php include 'header.php'; ?>

    <?php 
    if(isset($_GET['page'])){
        if($_GET['page'] == 'login'){
            include 'login.php';
        }
        else if($_GET['page'] == 'emission'){
            include 'Emission.php';
        }

        else{
            include 'Accueil.php'; 
        }
    }
    else{
        include 'Accueil.php'; 
    }
    ?>
    <?php include 'footer.php'; ?>
    
</body>


</html>