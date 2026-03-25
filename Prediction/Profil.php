<?php
$isLoggedIn = isset($_COOKIE['is_logged_in']) && $_COOKIE['is_logged_in'] === '1';
if (!$isLoggedIn) {
    echo "<div class='profil-container'>
    <h1 class='Titre'>Profil</h1>
    <p class='profil-info'>Veuillez vous connecter.</p>
    </div>";
    return;
}

$parieurId = isset($_COOKIE['user_id']) ? (int) $_COOKIE['user_id'] : 0;
$pseudo = isset($_COOKIE['user_pseudo']) ? htmlspecialchars($_COOKIE['user_pseudo']) : 'Inconnu';
$email = isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : 'Inconnu';

$pronostics = array();
$pronosticError = '';

if ($parieurId > 0) {
    $mysqli = getDatabaseConnection();

    if ($mysqli === null) {
        $pronosticError = 'Impossible de recuperer vos paris (connexion base de donnees).';
    } else {
        $sql = "
            SELECT
                p.pronostic_id,
                p.pronostic_score_equipe_1,
                p.pronostic_score_equipe_2,
                m.matchs_id,
                m.Date,
                m.equipe_1_id,
                m.equipe_2_id
            FROM pronostic p
            INNER JOIN matchs m ON m.matchs_id = p.matchs_id
            WHERE p.parieur_id = ?
            ORDER BY m.Date DESC, p.pronostic_id DESC
        ";

        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $parieurId);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $pronostics[] = $row;
            }
            $stmt->close();
        } else {
            $pronosticError = 'Impossible de recuperer vos paris (requete SQL invalide).';
        }

        $equipeMap = getEquipeNameMap($mysqli);
        $mysqli->close();

        if (!empty($pronostics)) {
            foreach ($pronostics as &$pronostic) {
                $equipe1Id = (int) $pronostic['equipe_1_id'];
                $equipe2Id = (int) $pronostic['equipe_2_id'];

                $pronostic['equipe_1_nom'] = isset($equipeMap[$equipe1Id]) ? $equipeMap[$equipe1Id] : ('Equipe ' . $equipe1Id);
                $pronostic['equipe_2_nom'] = isset($equipeMap[$equipe2Id]) ? $equipeMap[$equipe2Id] : ('Equipe ' . $equipe2Id);
            }
            unset($pronostic);
        }
    }
}
?>

<div class="profil-container">
    <h1 class="Titre">Profil de l'utilisateur</h1>

    <div class="profil-info">
        <p>Nom : <?php echo $pseudo; ?></p>
        <p>Email : <?php echo $email; ?></p>
    </div>

    <div class="profil-pronostics">
        <h2>Mes paris</h2>

        <?php if ($pronosticError !== ''): ?>
            <p class="profil-pronostics-empty"><?php echo htmlspecialchars($pronosticError); ?></p>
        <?php elseif (empty($pronostics)): ?>
            <p class="profil-pronostics-empty">Vous n'avez encore fait aucun pari.</p>
        <?php else: ?>
            <ul class="profil-pronostics-list">
                <?php foreach ($pronostics as $pronostic): ?>
                    <li class="profil-pronostics-item">
                        <p class="profil-pronostics-match">
                            <?php echo htmlspecialchars($pronostic['equipe_1_nom']); ?> vs <?php echo htmlspecialchars($pronostic['equipe_2_nom']); ?>
                        </p>
                        <p class="profil-pronostics-date">
                            Match du <?php echo htmlspecialchars((string) $pronostic['Date']); ?>
                        </p>
                        <p class="profil-pronostics-score">
                            Mon pronostic : <?php echo (int) $pronostic['pronostic_score_equipe_1']; ?> - <?php echo (int) $pronostic['pronostic_score_equipe_2']; ?>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>