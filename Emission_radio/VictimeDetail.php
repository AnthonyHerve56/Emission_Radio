<!-- Page detail Victime -->

<?php
$errorMessage = '';
$victime = null;
$victime_id = isset($_GET['victime_id']) ? intval($_GET['victime_id']) : 0;

if ($victime_id <= 0) {
    $errorMessage = "Identifiant de victime invalide.";
} else {
    $connection = getDbConnection();

    if ($connection === null) {
        $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
    } else {
        $victime = getVictimeById($connection, $victime_id);

        if ($victime === false) {
            $errorMessage = "Impossible de recuperer les informations de la victime.";
            $victime = null;
        } elseif ($victime === null) {
            $errorMessage = "Aucune victime trouvee avec cet identifiant.";
        }

        $connection->close();
    }
}
?>

<section class="form-container">
    <h1 class="Titre">Detail de la victime</h1>

    <?php if (!empty($errorMessage)): ?>
        <p class="form-error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php else: ?>
        <?php
        $nomComplet = $victime['victime_nom'] . '_' . $victime['victime_prenom'];
        $imageName = normalizeImageName($nomComplet);
        $imagePath = './img/' . $imageName . '.png';
        ?>

        <div class="form-body">
            <img class="entity-card-image" src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($victime['victime_nom'] . ' ' . $victime['victime_prenom']); ?>">

            <table class="emission-table" style="min-width: 100%; margin-top: 12px;">
                <tbody>
                    <tr>
                        <th>Nom</th>
                        <td><?php echo htmlspecialchars($victime['victime_nom']); ?></td>
                    </tr>
                    <tr>
                        <th>Prenom</th>
                        <td><?php echo htmlspecialchars($victime['victime_prenom']); ?></td>
                    </tr>
                    <tr>
                        <th>Ecole</th>
                        <td><?php echo htmlspecialchars($victime['victime_ecole']); ?></td>
                    </tr>
                    <tr>
                        <th>Evenement associe</th>
                        <td>
                            <?php
                            if (!empty($victime['evenement_date']) || !empty($victime['evenement_lieu'])) {
                                echo htmlspecialchars($victime['evenement_date'] . ' - ' . $victime['evenement_lieu']);
                            } else {
                                echo 'Non renseigne';
                            }
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="form-actions">
                <a class="btn-back" href="?page=victime">Retour a la liste</a>
            </div>
        </div>
    <?php endif; ?>
</section>
