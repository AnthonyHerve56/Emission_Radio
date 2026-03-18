<?php
// Config DB locale EasyPHP: adapte ces valeurs a ta base.
$dbHost = '127.0.0.1';
$dbName = 'db_prediction';
$dbUser = 'root';
$dbPass = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Index.php?page=login');
    exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $password === '') {
    header('Location: Index.php?page=login&error=1');
    exit();
}

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($mysqli->connect_error) {
    header('Location: Index.php?page=login&error=1');
    exit();
}

$mysqli->set_charset('utf8mb4');

$stmt = $mysqli->prepare('SELECT parieur_id, parieur_pseudo, parieur_email, parieur_mdp FROM parieur WHERE parieur_email = ? LIMIT 1');
if ($stmt) {
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    $isValid = false;
    if ($user) {
        $storedPassword = (string) $user['parieur_mdp'];

        // Accepte un mot de passe hash (recommande) ou texte simple (mode debutant).
        if (password_verify($password, $storedPassword) || hash_equals($storedPassword, $password)) {
            $isValid = true;
        }
    }

    if ($isValid) {
        // Cookies simples: email, pseudo et etat de connexion.
        setcookie('user_email', $user['parieur_email'], time() + (7 * 24 * 60 * 60), '/');
        setcookie('user_pseudo', $user['parieur_pseudo'], time() + (7 * 24 * 60 * 60), '/');
        setcookie('is_logged_in', '1', time() + (7 * 24 * 60 * 60), '/');

        $mysqli->close();
        header('Location: Index.php');
        exit();
    }
}

$mysqli->close();

header('Location: Index.php?page=login&error=1');
exit();
