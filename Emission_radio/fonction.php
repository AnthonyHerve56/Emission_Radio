<?php
/* Fonctions utilitaires pour l'acces a la base de donnees */

function getDbConnection()
{
	// Parametres par defaut d'une installation EasyPHP locale.
	// Si besoin, modifiez ici selon votre environnement.
	$host = '127.0.0.1';
	$user = 'root';
	$password = '';
	$database = 'db_emission';

	// @ evite d'afficher un warning PHP brut a l'ecran.
	// On gere ensuite l'echec de connexion proprement.
	$connection = @new mysqli($host, $user, $password, $database);

	// Si la connexion echoue, on renvoie null pour que la page decide du message a afficher.
	if ($connection->connect_errno) {
		return null;
	}

	// Important pour gerer correctement les caracteres accentues en base.
	$connection->set_charset('utf8');

	return $connection;
}

function getEmissionsOrderedDesc($connection)
{
	// Tri decroissant: les emissions les plus recentes apparaissent en premier.
	$sql = "SELECT emission_id, emission_heure_debut, emission_heure_fin, sujets FROM emissions ORDER BY emission_heure_debut DESC";
	$result = $connection->query($sql);

	// false signifie que la requete SQL a echoue.
	if ($result === false) {
		return false;
	}

	// On convertit le resultat SQL en tableau PHP simple.
	$emissions = array();

	while ($row = $result->fetch_assoc()) {
		$emissions[] = $row;
	}

	// Bonne pratique: liberer la ressource SQL.
	$result->free();

	return $emissions;
}

function getEvenementsOrderedDesc($connection)
{
	// Tri par date decroissante, puis par ID decroissant pour stabiliser l'ordre si meme date.
	// LEFT JOIN pour recuperer le nom de la menace associee a chaque evenement.
	$sql = "SELECT e.evenement_id, e.evenement_lieu, e.evenement_date, e.evenement_description, e.menace_id, m.menace_nom
			FROM evenement e
			LEFT JOIN menace m ON m.menace_id = e.menace_id
			ORDER BY e.evenement_date DESC, e.evenement_id DESC";
	$result = $connection->query($sql);

	if ($result === false) {
		return false;
	}

	$evenements = array();

	while ($row = $result->fetch_assoc()) {
		$evenements[] = $row;
	}

	$result->free();

	return $evenements;
}

function getMenacesOrderedByName($connection)
{
	// Affichage alphabetique pour une recherche visuelle plus rapide.
	$sql = "SELECT menace_id, menace_nom, menace_taille, menace_poids FROM menace ORDER BY menace_nom ASC";
	$result = $connection->query($sql);

	if ($result === false) {
		return false;
	}

	$menaces = array();

	while ($row = $result->fetch_assoc()) {
		$menaces[] = $row;
	}

	$result->free();

	return $menaces;
}

function getVictimesOrderedByName($connection)
{
	// Tri par nom puis prenom
	$sql = "SELECT victime_id, victime_nom, victime_prenom, victime_ecole, evenement_id FROM victime ORDER BY victime_nom ASC, victime_prenom ASC";
	$result = $connection->query($sql);

	if ($result === false) {
		return false;
	}

	$victimes = array();

	while ($row = $result->fetch_assoc()) {
		$victimes[] = $row;
	}

	$result->free();

	return $victimes;
}

function normalizeImageName($text)
{
	// Cette fonction transforme un texte libre en nom de fichier propre:
	// - minuscules
	// - accents supprimes
	// - espaces/tirets => underscore
	// - caracteres speciaux retires
	$text = trim($text);
	$text = strtolower($text);
	$translit = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

	if ($translit !== false) {
		$text = strtolower($translit);
	}

	$text = str_replace(array(' ', '-'), '_', $text);
	$text = preg_replace('/[^a-z0-9_]/', '', $text);
	$text = preg_replace('/_+/', '_', $text);

	// On retire les underscores en debut/fin pour un nom final propre.
	return trim($text, '_');
}

// ========== FONCTIONS POUR LES FORMULAIRES ==========

function insertEmission($connection, $heure_debut, $heure_fin, $sujets, $evenement_id)
{
	// Protection contre les injections SQL via des requetes preparees.
	$stmt = $connection->prepare("INSERT INTO emissions (emission_heure_debut, emission_heure_fin, sujets, evenement_id) VALUES (?, ?, ?, ?)");

	if ($stmt === false) {
		return false;
	}

	// Types: s=string, d=double (pour datetime), i=integer
	$stmt->bind_param('sssi', $heure_debut, $heure_fin, $sujets, $evenement_id);
	if ($stmt->execute()) {
		$stmt->close();
		return true;
	} else {
		$stmt->close();
		return false;
	}
}

function insertEvenement($connection, $lieu, $date, $description, $menace_id)
{
	// Requete preparee pour eviter les injections SQL.
	$stmt = $connection->prepare("INSERT INTO evenement (evenement_lieu, evenement_date, evenement_description, menace_id) VALUES (?, ?, ?, ?)");

	if ($stmt === false) {
		return false;
	}

	$stmt->bind_param('sssi', $lieu, $date, $description, $menace_id);
	if ($stmt->execute()) {
		$stmt->close();
		return true;
	} else {
		$stmt->close();
		return false;
	}
}

function insertMenace($connection, $nom, $taille, $poids)
{
	// Insertion dans la table menace.
	$stmt = $connection->prepare("INSERT INTO menace (menace_nom, menace_taille, menace_poids) VALUES (?, ?, ?)");

	if ($stmt === false) {
		return false;
	}

	$stmt->bind_param('sii', $nom, $taille, $poids);
	if ($stmt->execute()) {
		$stmt->close();
		return true;
	} else {
		$stmt->close();
		return false;
	}
}

function insertVictime($connection, $nom, $prenom, $ecole, $evenement_id)
{
	// Insertion dans la table victime.
	$stmt = $connection->prepare("INSERT INTO victime (victime_nom, victime_prenom, victime_ecole, evenement_id) VALUES (?, ?, ?, ?)");

	if ($stmt === false) {
		return false;
	}

	$stmt->bind_param('sssi', $nom, $prenom, $ecole, $evenement_id);
	if ($stmt->execute()) {
		$stmt->close();
		return true;
	} else {
		$stmt->close();
		return false;
	}
}

function getEvenementsForCombo($connection)
{
	// Liste simple pour les combos de formulaires (sans la jointure menace).
	$sql = "SELECT evenement_id, CONCAT(evenement_date, ' - ', evenement_lieu) as label FROM evenement ORDER BY evenement_date DESC";
	$result = $connection->query($sql);

	if ($result === false) {
		return false;
	}

	$evenements = array();

	while ($row = $result->fetch_assoc()) {
		$evenements[] = $row;
	}

	$result->free();

	return $evenements;
}

function getMenacesForCombo($connection)
{
	// Liste simple pour les combos de formulaires.
	$sql = "SELECT menace_id, menace_nom FROM menace ORDER BY menace_nom ASC";
	$result = $connection->query($sql);

	if ($result === false) {
		return false;
	}

	$menaces = array();

	while ($row = $result->fetch_assoc()) {
		$menaces[] = $row;
	}

	$result->free();

	return $menaces;
}

function getMenaceById($connection, $menace_id)
{
	// Fiche detail d'une menace.
	$stmt = $connection->prepare("SELECT menace_id, menace_nom, menace_taille, menace_poids FROM menace WHERE menace_id = ? LIMIT 1");

	if ($stmt === false) {
		return false;
	}

	$stmt->bind_param('i', $menace_id);

	if (!$stmt->execute()) {
		$stmt->close();
		return false;
	}

	$result = $stmt->get_result();
	$menace = $result->fetch_assoc();
	$stmt->close();

	if ($menace === null) {
		return null;
	}

	return $menace;
}

function getVictimeById($connection, $victime_id)
{
	// Fiche detail d'une victime avec infos evenement associe.
	$sql = "SELECT v.victime_id, v.victime_nom, v.victime_prenom, v.victime_ecole, v.evenement_id,
				e.evenement_lieu, e.evenement_date
			FROM victime v
			LEFT JOIN evenement e ON e.evenement_id = v.evenement_id
			WHERE v.victime_id = ?
			LIMIT 1";

	$stmt = $connection->prepare($sql);

	if ($stmt === false) {
		return false;
	}

	$stmt->bind_param('i', $victime_id);

	if (!$stmt->execute()) {
		$stmt->close();
		return false;
	}

	$result = $stmt->get_result();
	$victime = $result->fetch_assoc();
	$stmt->close();

	if ($victime === null) {
		return null;
	}

	return $victime;
}

