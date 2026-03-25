<!-- Page Evenement : liste des evenements en ordre chronologique decroissant -->

<?php
// Variables d'etat de la page.
$errorMessage = '';
$evenements = array();

// Connexion BDD.
$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
} else {
    // Recuperation des evenements les plus recents en premier.
    $evenements = getEvenementsOrderedDesc($connection);

    if ($evenements === false) {
        // Echec SQL: message explicite + tableau vide.
        $errorMessage = "Impossible de recuperer les evenements depuis la table evenement.";
        $evenements = array();
    }

    // Fermeture propre de la connexion.
    $connection->close();
}
?>

<section class="emission-container">
    <div class="list-header">
        <h1 class="Titre">Liste des evenements</h1>
        <a class="btn-add" href="?page=evenement_form">+ Ajouter</a>
    </div>

    <p class="emission-intro">Nombre d'évènements repportés : <?php echo count($evenements); ?></p>

    <div class="emission-table-wrapper">
        <table class="emission-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Description</th>
                    <th>Menace</th>
                </tr>
            </thead>
            <tbody>
                <!-- Gestion des 3 cas: erreur, vide, donnees -->
                <?php if (!empty($errorMessage)): ?>
                    <tr>
                        <td colspan="5"><?php echo htmlspecialchars($errorMessage); ?></td>
                    </tr>
                <?php elseif (empty($evenements)): ?>
                    <tr>
                        <td colspan="5">Aucun evenement enregistre pour le moment.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($evenements as $evenement): ?>
                        <tr>
                            <!-- Protection XSS avec htmlspecialchars sur toutes les colonnes -->
                            <td><?php echo htmlspecialchars($evenement['evenement_id']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($evenement['evenement_date']))); ?></td>
                            <td><?php echo htmlspecialchars($evenement['evenement_lieu']); ?></td>
                            <td><?php echo htmlspecialchars($evenement['evenement_description']); ?></td>
                            <td><?php echo htmlspecialchars(!empty($evenement['menace_nom']) ? $evenement['menace_nom'] : 'Non renseignee'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
