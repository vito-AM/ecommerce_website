<?php
session_start();
require_once("bd.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    header('Location: connexion.php');
    exit();
}

$bdd = getBD();
$id_client = $_SESSION['client']['id'];

// Récupérer l'historique des commandes
$sql = "SELECT c.id_commande, c.id_art, a.nom, a.prix, c.quantite, c.envoi 
        FROM Commandes c 
        JOIN articles a ON c.id_art = a.id_art 
        WHERE c.id_client = ? 
        ORDER BY c.id_commande DESC";

$stmt = $bdd->prepare($sql);
$stmt->execute([$id_client]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Historique des commandes - Chrono & Co.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="styles/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="styles/index.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="styles/historique.css" type="text/css" media="screen" />
</head>
<body>
    <header>
        <h1>Chrono & Co.</h1>
    </header>
    <main>
        <h2>Historique des commandes</h2>
        
        <?php if (empty($commandes)): ?>
            <p class="phrase_historique">Vous n'avez pas encore passé de commande.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID Commande</th>
                    <th>ID Article</th>
                    <th>Nom</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>État</th>
                </tr>
                <?php foreach ($commandes as $commande): ?>
                    <tr>
                        <td><?php echo($commande['id_commande']); ?></td>
                        <td><?php echo($commande['id_art']); ?></td>
                        <td><?php echo($commande['nom']); ?></td>
                        <td><?php echo($commande['prix']); ?> €</td>
                        <td><?php echo($commande['quantite']); ?></td>
                        <td><?php echo $commande['envoi'] ? 'Envoyée' : 'En attente'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <a href="index.php" class="retour_historique">Retour</a>
    </main>
    <footer></footer>
</body>
</html>