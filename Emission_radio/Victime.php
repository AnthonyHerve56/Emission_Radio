<?php
$errorMessage = '';
$victimes = array();

$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
} else {
    $victimes = getVictimesOrderedByName($connection);

    if ($victimes === false) {
        $errorMessage = "Impossible de recuperer les victimes depuis la table victime.";
        $victimes = array();
    }

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
                $nomComplet = $victime['victime_nom'] . '_' . $victime['victime_prenom'];
                $imageName = normalizeImageName($nomComplet);
                $imagePath = './img/' . $imageName . '.png';
                ?>
                <a class="entity-card" href="?page=victime_detail&victime_id=<?php echo urlencode($victime['victime_id']); ?>">
                    <img class="entity-card-image" src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($victime['victime_nom'] . ' ' . $victime['victime_prenom']); ?>">
                    <h2 class="entity-card-title"><?php echo htmlspecialchars($victime['victime_nom'] . ' ' . $victime['victime_prenom']); ?></h2>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
