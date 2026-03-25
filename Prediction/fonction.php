<?php

// -----------------------------------------------------------------------------
// Fichier: fonction.php
// Role:
// - Ouvrir la connexion a la base de donnees.
// - Recuperer les noms des equipes (mapping ID -> nom).
// - Afficher la liste des matchs sur la page d'accueil.
// -----------------------------------------------------------------------------

// Ouvre une connexion MySQLi vers la base locale.
// Retourne:
// - objet mysqli si tout se passe bien
// - null si la connexion echoue
function getDatabaseConnection() {
    // Parametres BDD (EasyPHP local).
    // A adapter si ton host / nom de base / user changent.
    $dbHost = '127.0.0.1';
    $dbName = 'db_prediction';
    $dbUser = 'root';
    $dbPass = '';

    // @ masque le warning PHP brut en cas d'erreur de connexion.
    // On gere proprement avec connect_error juste apres.
    $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);

    // Si la connexion a echoue, on retourne null
    // pour laisser la fonction appelante afficher un message propre.
    if ($mysqli->connect_error) {
        return null;
    }

    // Encodage UTF-8 complet pour les accents et caracteres speciaux.
    $mysqli->set_charset('utf8mb4');

    // Connexion valide.
    return $mysqli;
}

// Construit un dictionnaire des equipes:
//   [id_equipe => nom_equipe]
//
// Pourquoi ce systeme "souple" ?
// - Selon les projets, les tables/colonnes peuvent avoir des noms differents.
// - On tente plusieurs possibilites pour rester compatible.
//
// Parametre:
// - $mysqli: connexion deja ouverte.
//
// Retour:
// - tableau associatif ID -> nom
// - tableau vide si aucune table/colonne compatible n'est trouvee
function getEquipeNameMap($mysqli) {
    // Noms possibles de la table equipe dans la BDD.
    $tableCandidates = array('equipes', 'equipe');

    // Noms possibles de la colonne ID.
    $idCandidates = array('equipe_id', 'id');

    // Noms possibles de la colonne contenant le nom de l'equipe.
    $nameCandidates = array('equipe_nom', 'nom_equipe', 'nom', 'equipe_name', 'name');

    // On teste les tables candidates une par une.
    foreach ($tableCandidates as $table) {
        // Recupere la structure de la table pour detecter les colonnes existantes.
        $columnsResult = $mysqli->query("SHOW COLUMNS FROM `" . $table . "`");
        if (!$columnsResult) {
            // Table absente ou inaccessible -> on passe a la suivante.
            continue;
        }

        // Liste des noms de colonnes reelles dans la table.
        $columns = array();
        while ($col = $columnsResult->fetch_assoc()) {
            $columns[] = $col['Field'];
        }

        // Recherche de la colonne ID parmi les options connues.
        $idColumn = null;
        foreach ($idCandidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                $idColumn = $candidate;
                break;
            }
        }

        // Recherche de la colonne NOM parmi les options connues.
        $nameColumn = null;
        foreach ($nameCandidates as $candidate) {
            if (in_array($candidate, $columns, true)) {
                $nameColumn = $candidate;
                break;
            }
        }

        // Si on n'a pas trouve les 2 colonnes necessaires, table non exploitable.
        if ($idColumn === null || $nameColumn === null) {
            continue;
        }

        // Requete de mapping: id + nom de toutes les equipes.
        $result = $mysqli->query(
            "SELECT `" . $idColumn . "` AS equipe_id, `" . $nameColumn . "` AS equipe_nom FROM `" . $table . "`"
        );

        if (!$result) {
            // Requete KO -> table suivante.
            continue;
        }

        // Construction du dictionnaire [id => nom].
        $map = array();
        while ($row = $result->fetch_assoc()) {
            $map[(int) $row['equipe_id']] = (string) $row['equipe_nom'];
        }

        // Des qu'on a un mapping valide, on le retourne.
        if (!empty($map)) {
            return $map;
        }
    }

    // Aucun mapping trouve.
    return array();
}

// Affiche les matchs sur la page d'accueil.
// Cette fonction:
// 1) ouvre la connexion,
// 2) recupere les noms d'equipe,
// 3) lit les matchs,
// 4) genere le HTML de la grille.
function afficherMatch() {
    // Connexion BDD.
    $mysqli = getDatabaseConnection();

    // Titre de section, toujours affiche meme en cas d'erreur BDD.
    echo "<h2>Prochains matchs :</h2>";

    // Si BDD indisponible, on affiche un message lisible et on stoppe.
    if ($mysqli === null) {
        echo "<p>Impossible de recuperer les matchs (connexion base de donnees).</p>";
        return;
    }

    // Mapping des noms d'equipes (si table equipe detectee).
    $equipeNameMap = getEquipeNameMap($mysqli);

    // Lecture de tous les matchs tries par date croissante.
    $sql = "SELECT matchs_id, equipe_1_id, equipe_2_id, Date, equipe_score_equipe_1, equipe_score_equipe_2 FROM matchs ORDER BY Date ASC";
    $result = $mysqli->query($sql);

    // Si aucun match, on affiche un message et on ferme la connexion.
    if (!$result || $result->num_rows === 0) {
        echo "<p>Aucun match disponible pour le moment.</p>";
        $mysqli->close();
        return;
    }

    // Debut de la grille de cartes de match.
    echo "<ul class='match-list'>";

    // Parcours de chaque ligne "match" de la BDD.
    while ($match = $result->fetch_assoc()) {
        // Cast explicite en int pour eviter les surprises de type.
        $matchId = (int) $match['matchs_id'];
        $equipe1Id = (int) $match['equipe_1_id'];
        $equipe2Id = (int) $match['equipe_2_id'];

        // Utilise le vrai nom si present dans le mapping,
        // sinon fallback "Equipe X".
        $equipe1 = isset($equipeNameMap[$equipe1Id]) ? $equipeNameMap[$equipe1Id] : ('Equipe ' . $equipe1Id);
        $equipe2 = isset($equipeNameMap[$equipe2Id]) ? $equipeNameMap[$equipe2Id] : ('Equipe ' . $equipe2Id);

        // Protection XSS sur la date (texte injecte dans du HTML).
        $date = htmlspecialchars((string) $match['Date']);

        // Scores castes en int.
        $score1 = (int) $match['equipe_score_equipe_1'];
        $score2 = (int) $match['equipe_score_equipe_2'];

        // Image fixe simple:
        // - le nom de fichier depend de l'ID du match
        // - exemple: matchs_id=2 -> images/match_2.jpeg
        // Attention: il faut que l'image existe dans /images.
        $imageNumber = $matchId;
        $imagePath = 'images/match_' . $imageNumber . '.jpeg';

        // Carte HTML du match.
        echo "<li class='match-item'>";

        // URL de detail du match avec parametres GET.
        // urlencode securise les valeurs placees dans l'URL.
        echo "<a href='index.php?page=match&matchs_id=" . $matchId . "&equipe1=" . urlencode($equipe1) . "&equipe2=" . urlencode($equipe2) . "&date=" . urlencode($date) . "'>";

        // Image + alt descriptif (accessibilite).
        echo "<img src='" . htmlspecialchars($imagePath) . "' alt='" . htmlspecialchars($equipe1) . " vs " . htmlspecialchars($equipe2) . "'>";

        // Bloc texte: equipes, date, score.
        echo "<div class='match-info'>";
        echo "<span class='match-equipes'>" . htmlspecialchars($equipe1) . " <strong>VS</strong> " . htmlspecialchars($equipe2) . "</span>";
        echo "<span class='match-date'>Date : " . $date . "</span>";
        echo "<span class='match-date'>Score : " . $score1 . " - " . $score2 . "</span>";
        echo "</div>";
        echo "</a>";
        echo "</li>";
    }

    // Fin de la grille.
    echo "</ul>";

    // Fermeture propre de la connexion.
    $mysqli->close();
}




?>