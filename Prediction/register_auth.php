<?php
// Parametres de connexion MySQL (EasyPHP local).
$dbHost = '127.0.0.1';
$dbName = 'db_prediction';
$dbUser = 'root';
$dbPass = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: Index.php?page=register');
    exit();
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$pseudo = isset($_POST['pseudo']) ? trim($_POST['pseudo']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $pseudo === '' || $password === '') {
    header('Location: Index.php?page=register&error=missing');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: Index.php?page=register&error=invalid_email');
    exit();
}

if (strlen($password) < 6) {
    header('Location: Index.php?page=register&error=password_short');
    exit();
}

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($mysqli->connect_error) {
    header('Location: Index.php?page=register&error=db');
    exit();
}

$mysqli->set_charset('utf8mb4');

// Verifie que la colonne parieur_mdp peut stocker un hash SHA1 (40 caracteres).
$schemaOk = true;
$columnResult = $mysqli->query("SHOW COLUMNS FROM parieur LIKE 'parieur_mdp'");
if ($columnResult) {
    $column = $columnResult->fetch_assoc();
    if ($column && isset($column['Type'])) {
        if (preg_match('/varchar\((\d+)\)/i', (string) $column['Type'], $matches)) {
            $maxLength = (int) $matches[1];
            if ($maxLength < 40) {
                $schemaOk = false;
            }
        }
    }
}

if (!$schemaOk) {
    $mysqli->close();
    header('Location: Index.php?page=register&error=schema_mdp');
    exit();
}

$checkStmt = $mysqli->prepare('SELECT parieur_id FROM parieur WHERE parieur_email = ? OR parieur_pseudo = ? LIMIT 1');
if (!$checkStmt) {
    $mysqli->close();
    header('Location: Index.php?page=register&error=db');
    exit();
}

$checkStmt->bind_param('ss', $email, $pseudo);
$checkStmt->execute();
$existsResult = $checkStmt->get_result();
$existsUser = $existsResult ? $existsResult->fetch_assoc() : null;
$checkStmt->close();

if ($existsUser) {
    $mysqli->close();
    header('Location: Index.php?page=register&error=exists');
    exit();
}

$hashedPassword = sha1($password);

$insertStmt = $mysqli->prepare('INSERT INTO parieur (parieur_pseudo, parieur_email, parieur_mdp) VALUES (?, ?, ?)');
if (!$insertStmt) {
    $mysqli->close();
    header('Location: Index.php?page=register&error=db');
    exit();
}

$insertStmt->bind_param('sss', $pseudo, $email, $hashedPassword);
$isInserted = $insertStmt->execute();
$insertStmt->close();
$mysqli->close();

if (!$isInserted) {
    header('Location: Index.php?page=register&error=db');
    exit();
}

header('Location: Index.php?page=login&registered=1');
exit();
