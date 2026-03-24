<?php
// Parametres de connexion MySQL (EasyPHP local).
$dbHost = '127.0.0.1';
$dbName = 'db_prediction';
$dbUser = 'root';
$dbPass = '';

// Cette page traite seulement le submit du formulaire.
// Si on arrive ici autrement, on renvoie vers la page login.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Index.php?page=login');
    exit();
}

// Recuperation des champs envoyes par le formulaire.
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validation minimale: les deux champs sont obligatoires.
if ($email === '' || $password === '') {
    header('Location: Index.php?page=login&error=1');
    exit();
}

// Ouverture de la connexion MySQLi.
// @ evite d'afficher un warning brut a l'ecran en cas d'erreur.
$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Si la connexion echoue, on revient au login avec erreur.
if ($mysqli->connect_error) {
    header('Location: Index.php?page=login&error=1');
    exit();
}

// Encodage recommande pour gerer correctement les caracteres speciaux.
$mysqli->set_charset('utf8mb4');

// Requete preparee pour eviter l'injection SQL.
$stmt = $mysqli->prepare('SELECT parieur_id, parieur_pseudo, parieur_email, parieur_mdp FROM parieur WHERE parieur_email = ? LIMIT 1');
if ($stmt) {
    // Bind de l'email puis execution.
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    // Verification du mot de passe.
    // On accepte un hash (password_hash/password_verify)
    // et aussi le texte simple pour rester compatible avec une base debutante.
    $isValid = false;
    if ($user) {
        $storedPassword = (string) $user['parieur_mdp'];

        if (password_verify($password, $storedPassword) || hash_equals($storedPassword, $password)) {
            $isValid = true;
        }
    }

    if ($isValid) {
        // Cookies simples de session "persistante" (7 jours).
        // Ils servent a afficher l'etat connecte et des infos utilisateur.
        setcookie('user_id', (string) $user['parieur_id'], time() + (7 * 24 * 60 * 60), '/');
        setcookie('user_email', $user['parieur_email'], time() + (7 * 24 * 60 * 60), '/');
        setcookie('user_pseudo', $user['parieur_pseudo'], time() + (7 * 24 * 60 * 60), '/');
        setcookie('is_logged_in', '1', time() + (7 * 24 * 60 * 60), '/');

        // Connexion OK -> retour a l'accueil.
        $mysqli->close();
        header('Location: Index.php');
        exit();
    }
}

// Nettoyage de la connexion avant de quitter.
$mysqli->close();

// Echec d'authentification -> retour au login avec erreur.
header('Location: Index.php?page=login&error=1');
exit();
