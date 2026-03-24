<?php
// Informations du match transmises depuis la page d'accueil.
$matchsId = isset($_GET['matchs_id']) ? (int) $_GET['matchs_id'] : 0;
$equipe_1 = isset($_GET['equipe1']) ? urldecode($_GET['equipe1']) : 'Equipe 1';
$equipe_2 = isset($_GET['equipe2']) ? urldecode($_GET['equipe2']) : 'Equipe 2';
$date = isset($_GET['date']) ? urldecode($_GET['date']) : '';

// Si on a l'ID du match, on recharge les vraies infos en base
// pour afficher les noms d'equipes plutot que des IDs/fallbacks.
if ($matchsId > 0) {
    $mysqliMatch = getDatabaseConnection();
    if ($mysqliMatch !== null) {
        $stmtMatch = $mysqliMatch->prepare('SELECT equipe_1_id, equipe_2_id, Date FROM matchs WHERE matchs_id = ? LIMIT 1');
        if ($stmtMatch) {
            $stmtMatch->bind_param('i', $matchsId);
            $stmtMatch->execute();
            $resMatch = $stmtMatch->get_result();
            $matchRow = $resMatch ? $resMatch->fetch_assoc() : null;
            $stmtMatch->close();

            if ($matchRow) {
                $map = getEquipeNameMap($mysqliMatch);

                $equipe1Id = (int) $matchRow['equipe_1_id'];
                $equipe2Id = (int) $matchRow['equipe_2_id'];

                $equipe_1 = isset($map[$equipe1Id]) ? $map[$equipe1Id] : ('Equipe ' . $equipe1Id);
                $equipe_2 = isset($map[$equipe2Id]) ? $map[$equipe2Id] : ('Equipe ' . $equipe2Id);
                $date = (string) $matchRow['Date'];
            }
        }
        $mysqliMatch->close();
    }
}

// Etat utilisateur connecte via cookies.
$isLoggedIn = isset($_COOKIE['is_logged_in']) && $_COOKIE['is_logged_in'] === '1';
$parieurId = isset($_COOKIE['user_id']) ? (int) $_COOKIE['user_id'] : 0;

$message = '';

// Insertion du pronostic seulement si formulaire soumis par un utilisateur connecte.
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['score1']) &&
    isset($_POST['score2']) &&
    $isLoggedIn &&
    $parieurId > 0 &&
    $matchsId > 0
) {
    $score1 = (int) $_POST['score1'];
    $score2 = (int) $_POST['score2'];

    $mysqli = getDatabaseConnection();
    if ($mysqli === null) {
        $message = "Erreur: connexion base de donnees impossible.";
    } else {
        $stmt = $mysqli->prepare(
            'INSERT INTO pronostic (matchs_id, parieur_id, pronostic_score_equipe_1, pronostic_score_equipe_2) VALUES (?, ?, ?, ?)'
        );

        if ($stmt) {
            $stmt->bind_param('iiii', $matchsId, $parieurId, $score1, $score2);
            if ($stmt->execute()) {
                $message = "Pronostic enregistre.";
            } else {
                $message = "Erreur lors de l'enregistrement du pronostic.";
            }
            $stmt->close();
        } else {
            $message = "Erreur SQL: impossible de preparer la requete.";
        }

        $mysqli->close();
    }
}
?>

<h1 class="Titre">Match : <?php echo htmlspecialchars($equipe_1); ?> vs <?php echo htmlspecialchars($equipe_2); ?></h1>

<div class="match-page-container">
    <?php if ($date !== ''): ?>
        <p class="match-page-meta">Date du match : <?php echo htmlspecialchars($date); ?></p>
    <?php endif; ?>

    <?php if ($message !== ''): ?>
        <p class="match-page-message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
        <form method="POST" action="index.php?page=match&matchs_id=<?php echo $matchsId; ?>&equipe1=<?php echo urlencode($equipe_1); ?>&equipe2=<?php echo urlencode($equipe_2); ?>&date=<?php echo urlencode($date); ?>" class="match-form">
            <label for="score1">Score <?php echo htmlspecialchars($equipe_1); ?> :</label>
            <input type="number" id="score1" name="score1" min="0" required>

            <label for="score2">Score <?php echo htmlspecialchars($equipe_2); ?> :</label>
            <input type="number" id="score2" name="score2" min="0" required>

            <input type="submit" value="Valider le pronostic" class="btn-login">
        </form>
    <?php else: ?>
        <p class="match-page-message">Connecte-toi pour saisir un pronostic.</p>
    <?php endif; ?>
</div>
        