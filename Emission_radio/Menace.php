<!-- Page Menace : liste en cartes avec image + nom -->

<?php
// Variables de la page.
$errorMessage = '';
$menaces = array();

// Connexion a MySQL.
$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
} else {
    // Recuperation des menaces par ordre alphabetique.
    $menaces = getMenacesOrderedByName($connection);

    if ($menaces === false) {
        $errorMessage = "Impossible de recuperer les menaces depuis la table menace.";
        $menaces = array();
    }

    // On ferme la connexion apres la lecture.
    $connection->close();
}
?>

<section class="entity-container">
    <div class="list-header">
        <h1 class="Titre">Liste des menaces</h1>
        <a class="btn-add" href="?page=menace_form">+ Ajouter</a>
    </div>

    <p class="emission-intro">Nombre de menaces : <?php echo count($menaces); ?></p>

    <?php if (!empty($errorMessage)): ?>
        <p class="entity-message"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php elseif (empty($menaces)): ?>
        <p class="entity-message">Aucune menace enregistree pour le moment.</p>
    <?php else: ?>
        <div class="entity-grid">
            <?php foreach ($menaces as $menace): ?>
                <?php
                $imageName = normalizeImageName($menace['menace_nom']);
                $imagePath = './img/' . $imageName . '.png';
                ?>
                <a class="entity-card" href="?page=menace_detail&menace_id=<?php echo urlencode($menace['menace_id']); ?>">
                    <img class="entity-card-image" src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($menace['menace_nom']); ?>">
                    <h2 class="entity-card-title"><?php echo htmlspecialchars($menace['menace_nom']); ?></h2>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
