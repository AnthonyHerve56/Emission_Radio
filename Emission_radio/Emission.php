<!-- Page Emission : liste des emissions par ordre chronologique decroissant -->

<?php
// Variables d'affichage initialisees avant la lecture BDD.
$errorMessage = '';
$emissions = array();

// 1) Connexion a la base.
$connection = getDbConnection();

if ($connection === null) {
    // Message utilisateur simple en cas d'echec de connexion.
    $errorMessage = "Connexion a la base impossible. Verifiez vos parametres MySQL dans fonction.php.";
} else {
    // 2) Recuperation des emissions triees de la plus recente a la plus ancienne.
    $emissions = getEmissionsOrderedDesc($connection);

    if ($emissions === false) {
        // Si la requete SQL echoue, on garde un tableau vide et on affiche un message clair.
        $errorMessage = "Impossible de recuperer les emissions depuis la table emissions.";
        $emissions = array();
    }

    // 3) On ferme la connexion des que possible.
    $connection->close();
}
?>

<section class="emission-container">
    <div class="list-header">
        <h1 class="Titre">Liste des emissions</h1>
        <a class="btn-add" href="?page=emission_form">+ Ajouter</a>
    </div>

    <p class="emission-intro"> Nombre d'émissions : <?php echo count($emissions); ?></p>

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
                <!-- Cas 1: erreur de connexion ou de requete -->
                <?php if (!empty($errorMessage)): ?>
                    <tr>
                        <td colspan="4"><?php echo htmlspecialchars($errorMessage); ?></td>
                    </tr>
                <!-- Cas 2: aucun enregistrement en base -->
                <?php elseif (empty($emissions)): ?>
                    <tr>
                        <td colspan="4">Aucune emission enregistree pour le moment.</td>
                    </tr>
                <!-- Cas 3: donnees disponibles -->
                <?php else: ?>
                    <?php foreach ($emissions as $emission): ?>
                        <tr>
                            <!-- htmlspecialchars protege l'affichage HTML -->
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
