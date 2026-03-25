<!-- Page detail Menace -->

<?php
$errorMessage = '';
$menace = null;
$menace_id = isset($_GET['menace_id']) ? intval($_GET['menace_id']) : 0;

if ($menace_id <= 0) {
    $errorMessage = "Identifiant de menace invalide.";
} else {
    $connection = getDbConnection();

    if ($connection === null) {
        $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
    } else {
        $menace = getMenaceById($connection, $menace_id);

        if ($menace === false) {
            $errorMessage = "Impossible de recuperer les informations de la menace.";
            $menace = null;
        } elseif ($menace === null) {
            $errorMessage = "Aucune menace trouvee avec cet identifiant.";
        }

        $connection->close();
    }
}
?>

<section class="form-container">
    <h1 class="Titre">Detail de la menace</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="form-error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php else: ?>
        <?php
        $imageName = normalizeImageName($menace['menace_nom']);
        $imagePath = './img/' . $imageName . '.png';
        ?>

        <div class="form-body">
            <img class="entity-card-image" src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($menace['menace_nom']); ?>">

            <table class="emission-table" style="min-width: 100%; margin-top: 12px;">
                <tbody>
                    <tr>
                        <th>Nom</th>
                        <td><?php echo htmlspecialchars($menace['menace_nom']); ?></td>
                    </tr>
                    <tr>
                        <th>Taille</th>
                        <td><?php echo htmlspecialchars($menace['menace_taille']); ?> cm</td>
                    </tr>
                    <tr>
                        <th>Poids</th>
                        <td><?php echo htmlspecialchars($menace['menace_poids']); ?> kg</td>
                    </tr>
                </tbody>
            </table>

            <div class="form-actions">
                <a class="btn-back" href="?page=menace">Retour a la liste</a>
            </div>
        </div>
    <?php endif; ?>
</section>
