<?php
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    setcookie('user_id', '', time() - 3600, '/');
    setcookie('user_email', '', time() - 3600, '/');
    setcookie('user_pseudo', '', time() - 3600, '/');
    setcookie('is_logged_in', '', time() - 3600, '/');

    header('Location: Index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php
    include 'fonction.php';
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Anthony HERVE , Mohamed Ali SHAIEK">
    <title>Accueil_2</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <?php 
    if(isset($_GET['page'])){
        if($_GET['page'] == 'login'){
            include 'login.php';
        }
        else if($_GET['page'] == 'register'){
            include 'register.php';
        }
         else if($_GET['page'] == 'match'){
            include 'match.php';
        }
         else if($_GET['page'] == 'profil'){
            include 'Profil.php';
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