<?php
/* Fonctions utilitaires pour l'acces a la base de donnees */

function getDbConnection()
{
	$host = '127.0.0.1';
	$user = 'root';
	$password = '';
	$database = 'db_emission';

	$connection = @new mysqli($host, $user, $password, $database);

	if ($connection->connect_errno) {
		return null;
	}

	$connection->set_charset('utf8');

	return $connection;
}

function getEmissionsOrderedDesc($connection)
{
	$sql = "SELECT emission_id, emission_heure_debut, emission_heure_fin, sujets FROM emissions ORDER BY emission_heure_debut DESC";
	$result = $connection->query($sql);

	if ($result === false) {
		return false;
	}

	$emissions = array();

	while ($row = $result->fetch_assoc()) {
		$emissions[] = $row;
	}

	$result->free();

	return $emissions;
}

