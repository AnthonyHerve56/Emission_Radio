<!-- Formulaire d'ajout d'evenement -->

<?php
// Variables pour le formulaire.
$successMessage = '';
$errorMessage = '';
$formData = array('evenement_lieu' => '', 'evenement_date' => '', 'evenement_description' => '', 'menace_id' => '');
$menaces = array();

// Chargement des menaces pour le combo.
$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible.";
} else {
    $menaces = getMenacesForCombo($connection);
    
    if ($menaces === false) {
        $menaces = array();
    }
}

// Traitement du formulaire en POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lieu = isset($_POST['evenement_lieu']) ? trim($_POST['evenement_lieu']) : '';
    $date = isset($_POST['evenement_date']) ? trim($_POST['evenement_date']) : '';
    $description = isset($_POST['evenement_description']) ? trim($_POST['evenement_description']) : '';
    $menace_id = isset($_POST['menace_id']) ? intval($_POST['menace_id']) : 0;

    // Validation basique.
    if (empty($lieu) || empty($date) || empty($description) || $menace_id <= 0) {
        $errorMessage = "Tous les champs sont obligatoires.";
    } else {
        // Tentative d'insertion.
        if (insertEvenement($connection, $lieu, $date, $description, $menace_id)) {
            $successMessage = "Evenement ajoute avec succes !";
            $formData = array('evenement_lieu' => '', 'evenement_date' => '', 'evenement_description' => '', 'menace_id' => '');
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
    <h1 class="Titre">Ajouter un evenement</h1>

    <?php if (!empty($successMessage)): ?>
        <p class="form-success"><?php echo htmlspecialchars($successMessage); ?></p>
        <p><a href="?page=evenement" class="btn-back">Retour a la liste</a></p>
    <?php else: ?>
        <?php if (!empty($errorMessage)): ?>
            <p class="form-error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form method="POST" class="form-body">
            <div class="form-group">
                <label for="evenement_lieu">Lieu :</label>
                <input type="text" id="evenement_lieu" name="evenement_lieu" required value="<?php echo htmlspecialchars($formData['evenement_lieu']); ?>">
            </div>

            <div class="form-group">
                <label for="evenement_date">Date :</label>
                <input type="date" id="evenement_date" name="evenement_date" required value="<?php echo htmlspecialchars($formData['evenement_date']); ?>">
            </div>

            <div class="form-group">
                <label for="evenement_description">Description :</label>
                <textarea id="evenement_description" name="evenement_description" rows="5" required><?php echo htmlspecialchars($formData['evenement_description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="menace_id">Menace :</label>
                <select id="menace_id" name="menace_id" required>
                    <option value="">-- Choisir une menace --</option>
                    <?php foreach ($menaces as $menace): ?>
                        <option value="<?php echo htmlspecialchars($menace['menace_id']); ?>"><?php echo htmlspecialchars($menace['menace_nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Ajouter</button>
                <a href="?page=evenement" class="btn-cancel">Annuler</a>
            </div>
        </form>
    <?php endif; ?>
</section>
