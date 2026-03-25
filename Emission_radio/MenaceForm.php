<!-- Formulaire d'ajout de menace -->

<?php
// Variables pour le formulaire.
$successMessage = '';
$errorMessage = '';
$formData = array('menace_nom' => '', 'menace_taille' => '', 'menace_poids' => '');

// Connexion BDD.
$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible.";
}

// Traitement du formulaire en POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['menace_nom']) ? trim($_POST['menace_nom']) : '';
    $taille = isset($_POST['menace_taille']) ? intval($_POST['menace_taille']) : 0;
    $poids = isset($_POST['menace_poids']) ? intval($_POST['menace_poids']) : 0;

    // Validation basique.
    if (empty($nom) || $taille <= 0 || $poids <= 0) {
        $errorMessage = "Tous les champs sont obligatoires et doivent etre valides.";
    } else {
        // Tentative d'insertion.
        if ($connection !== null && insertMenace($connection, $nom, $taille, $poids)) {
            $successMessage = "Menace ajoutee avec succes !";
            $formData = array('menace_nom' => '', 'menace_taille' => '', 'menace_poids' => '');
        } else {
            $errorMessage = "Erreur lors de l'insertion. Verifiez les donnees.";
        }
    }

    if ($connection !== null) {
        $connection->close();
    }
}
?>

<section class="form-container">
    <h1 class="Titre">Ajouter une menace</h1>

    <?php if (!empty($successMessage)): ?>
        <p class="form-success"><?php echo htmlspecialchars($successMessage); ?></p>
        <p><a href="?page=menace" class="btn-back">Retour a la liste</a></p>
    <?php else: ?>
        <?php if (!empty($errorMessage)): ?>
            <p class="form-error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form method="POST" class="form-body">
            <div class="form-group">
                <label for="menace_nom">Nom :</label>
                <input type="text" id="menace_nom" name="menace_nom" required value="<?php echo htmlspecialchars($formData['menace_nom']); ?>">
            </div>

            <div class="form-group">
                <label for="menace_taille">Taille (cm) :</label>
                <input type="number" id="menace_taille" name="menace_taille" min="1" required value="<?php echo htmlspecialchars($formData['menace_taille']); ?>">
            </div>

            <div class="form-group">
                <label for="menace_poids">Poids (kg) :</label>
                <input type="number" id="menace_poids" name="menace_poids" min="1" required value="<?php echo htmlspecialchars($formData['menace_poids']); ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Ajouter</button>
                <a href="?page=menace" class="btn-cancel">Annuler</a>
            </div>
        </form>
    <?php endif; ?>
</section>
