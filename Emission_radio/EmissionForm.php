<!-- Formulaire d'ajout d'emission -->

<?php
// Variables pour le formulaire.
$successMessage = '';
$errorMessage = '';
$formData = array('emission_heure_debut' => '', 'emission_heure_fin' => '', 'sujets' => '', 'evenement_id' => '');
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
    $heure_debut = isset($_POST['emission_heure_debut']) ? trim($_POST['emission_heure_debut']) : '';
    $heure_fin = isset($_POST['emission_heure_fin']) ? trim($_POST['emission_heure_fin']) : '';
    $sujets = isset($_POST['sujets']) ? trim($_POST['sujets']) : '';
    $evenement_id = isset($_POST['evenement_id']) ? intval($_POST['evenement_id']) : 0;

    // Validation basique.
    if (empty($heure_debut) || empty($heure_fin) || empty($sujets) || $evenement_id <= 0) {
        $errorMessage = "Tous les champs sont obligatoires.";
    } else {
        // Tentative d'insertion.
        if (insertEmission($connection, $heure_debut, $heure_fin, $sujets, $evenement_id)) {
            $successMessage = "Emission ajoutee avec succes !";
            $formData = array('emission_heure_debut' => '', 'emission_heure_fin' => '', 'sujets' => '', 'evenement_id' => '');
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
    <h1 class="Titre">Ajouter une emission</h1>

    <?php if (!empty($successMessage)): ?>
        <p class="form-success"><?php echo htmlspecialchars($successMessage); ?></p>
        <p><a href="?page=emission" class="btn-back">Retour a la liste</a></p>
    <?php else: ?>
        <?php if (!empty($errorMessage)): ?>
            <p class="form-error"><?php echo htmlspecialchars($errorMessage); ?></p>
        <?php endif; ?>

        <form method="POST" class="form-body">
            <div class="form-group">
                <label for="emission_heure_debut">Heure de debut :</label>
                <input type="datetime-local" id="emission_heure_debut" name="emission_heure_debut" required value="<?php echo htmlspecialchars($formData['emission_heure_debut']); ?>">
            </div>

            <div class="form-group">
                <label for="emission_heure_fin">Heure de fin :</label>
                <input type="datetime-local" id="emission_heure_fin" name="emission_heure_fin" required value="<?php echo htmlspecialchars($formData['emission_heure_fin']); ?>">
            </div>

            <div class="form-group">
                <label for="sujets">Sujet :</label>
                <input type="text" id="sujets" name="sujets" required value="<?php echo htmlspecialchars($formData['sujets']); ?>">
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
                <a href="?page=emission" class="btn-cancel">Annuler</a>
            </div>
        </form>
    <?php endif; ?>
</section>
