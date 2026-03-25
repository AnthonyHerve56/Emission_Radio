<!-- Formulaire d'ajout de victime -->

<?php
// Variables pour le formulaire.
$successMessage = '';
$errorMessage = '';
$formData = array('victime_nom' => '', 'victime_prenom' => '', 'victime_ecole' => '', 'evenement_id' => '');
$evenements = array();

// Chargement des evenements pour le combo.
$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible.";
} else {
    $evenements = getEvenementsForCombo($connection);
    
    if ($evenements === false) {
        $evenements = array();
    }
}

// Traitement du formulaire en POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = isset($_POST['victime_nom']) ? trim($_POST['victime_nom']) : '';
    $prenom = isset($_POST['victime_prenom']) ? trim($_POST['victime_prenom']) : '';
    $ecole = isset($_POST['victime_ecole']) ? trim($_POST['victime_ecole']) : '';
    $evenement_id = isset($_POST['evenement_id']) ? intval($_POST['evenement_id']) : 0;

    // Validation basique.
    if (empty($nom) || empty($prenom) || empty($ecole) || $evenement_id <= 0) {
        $errorMessage = "Tous les champs sont obligatoires.";
    } else {
        // Tentative d'insertion.
        if ($connection !== null && insertVictime($connection, $nom, $prenom, $ecole, $evenement_id)) {
            $successMessage = "Victime ajoutee avec succes !";
            $formData = array('victime_nom' => '', 'victime_prenom' => '', 'victime_ecole' => '', 'evenement_id' => '');
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
    <h1 class="Titre">Ajouter une victime</h1>

    <?php if (!empty($successMessage)): ?>
        <p class="form-success"><?php echo htmlspecialchars($successMessage); ?></p>
        <p><a href="?page=victime" class="btn-back">Retour a la liste</a></p>
    <?php else: ?>
        <?php if (!empty($errorMessage)): ?>
            <p class="form-error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form method="POST" class="form-body">
            <div class="form-group">
                <label for="victime_nom">Nom :</label>
                <input type="text" id="victime_nom" name="victime_nom" required value="<?php echo htmlspecialchars($formData['victime_nom']); ?>">
            </div>

            <div class="form-group">
                <label for="victime_prenom">Prenom :</label>
                <input type="text" id="victime_prenom" name="victime_prenom" required value="<?php echo htmlspecialchars($formData['victime_prenom']); ?>">
            </div>

            <div class="form-group">
                <label for="victime_ecole">Ecole :</label>
                <input type="text" id="victime_ecole" name="victime_ecole" required value="<?php echo htmlspecialchars($formData['victime_ecole']); ?>">
            </div>

            <div class="form-group">
                <label for="evenement_id">Evenement :</label>
                <select id="evenement_id" name="evenement_id" required>
                    <option value="">-- Choisir un evenement --</option>
                    <?php foreach ($evenements as $evt): ?>
                        <option value="<?php echo htmlspecialchars($evt['evenement_id']); ?>"><?php echo htmlspecialchars($evt['label']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Ajouter</button>
                <a href="?page=victime" class="btn-cancel">Annuler</a>
            </div>
        </form>
    <?php endif; ?>
</section>
