<?php
$errorMessage = '';
$emissions = array();

$connection = getDbConnection();

if ($connection === null) {
    $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
} else {
    $emissions = getEmissionsOrderedDesc($connection);

    if ($emissions === false) {
        $errorMessage = "Impossible de recuperer les emissions depuis la table emissions.";
        $emissions = array();
    }

    $connection->close();
}
?>

<section class="emission-container">
    <div class="list-header">
        <h1 class="Titre">Liste des emissions</h1>
        <a class="btn-add" href="?page=emission_form">+ Ajouter</a>
    </div>

    <p class="emission-intro">Nombre d'emissions : <?php echo count($emissions); ?></p>

    <div class="emission-table-wrapper">
        <table class="emission-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Debut</th>
                    <th>Fin</th>
                    <th>Sujet</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($errorMessage)): ?>
                    <tr>
                        <td colspan="4"><?php echo htmlspecialchars($errorMessage); ?></td>
                    </tr>
                <?php elseif (empty($emissions)): ?>
                    <tr>
                        <td colspan="4">Aucune emission enregistree pour le moment.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($emissions as $emission): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($emission['emission_id']); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($emission['emission_heure_debut']))); ?></td>
                            <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($emission['emission_heure_fin']))); ?></td>
                            <td><?php echo htmlspecialchars($emission['sujets']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
