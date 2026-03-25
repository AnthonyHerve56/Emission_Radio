<!-- Page Victime : liste en cartes cliquables avec image + nom -->

<?php
// Variables de travail pour l'affichage.
$errorMessage = '';
$victimes = array();

// Connexion a la base de donnees.
$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
} else {
    // Recuperation de toutes les victimes triees par nom/prenom.
    $victimes = getVictimesOrderedByName($connection);

    if ($victimes === false) {
        $errorMessage = "Impossible de recuperer les victimes depuis la table victime.";
        $victimes = array();
    }

    // Fermeture immediate de la connexion.
    $connection->close();
}
?>

<section class="entity-container">
    <div class="list-header">
        <h1 class="Titre">Liste des victimes</h1>
        <a class="btn-add" href="?page=victime_form">+ Ajouter</a>
    </div>

    <p class="emission-intro">Nombre de victimes : <?php echo count($victimes); ?></p>

    <?php if (!empty($errorMessage)): ?>
        <p class="entity-message"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php elseif (empty($victimes)): ?>
        <p class="entity-message">Aucune victime enregistree pour le moment.</p>
    <?php else: ?>
        <div class="entity-grid">
            <?php foreach ($victimes as $victime): ?>
                <?php
                // Convention de nommage d'image basee sur "nom_prenom".
                // Exemple: "Byers" + "Will" => "./img/byers_will.png"
                $nomComplet = $victime['victime_nom'] . '_' . $victime['victime_prenom'];
                $imageName = normalizeImageName($nomComplet);
                $imagePath = './img/' . $imageName . '.png';
                ?>
                <!-- Carte cliquable: l'ID peut servir plus tard pour une page detail victime -->
                <a class="entity-card" href="?page=victime&victime_id=<?php echo urlencode($victime['victime_id']); ?>">
                    <img class="entity-card-image" src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($victime['victime_nom'] . ' ' . $victime['victime_prenom']); ?>">
                    <h2 class="entity-card-title"><?php echo htmlspecialchars($victime['victime_nom'] . ' ' . $victime['victime_prenom']); ?></h2>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
