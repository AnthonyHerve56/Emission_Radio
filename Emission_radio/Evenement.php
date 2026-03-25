<?php
$errorMessage = '';
$evenements = array();

$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
} else {
    $evenements = getEvenementsOrderedDesc($connection);

    if ($evenements === false) {
        $errorMessage = "Impossible de recuperer les evenements depuis la table evenement.";
        $evenements = array();
    }

    $connection->close();
}
?>

<section class="emission-container">
    <div class="list-header">
        <h1 class="Titre">Liste des evenements</h1>
        <a class="btn-add" href="?page=evenement_form">+ Ajouter</a>
    </div>

    <p class="emission-intro">Nombre d'evenements : <?php echo count($evenements); ?></p>

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
