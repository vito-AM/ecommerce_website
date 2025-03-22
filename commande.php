<?php
session_start();
require_once("bd.php");
require_once("token.php");

# on vérifie si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    header('Location: connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && !validateToken($_POST['token'])) {
  die("Session invalide ou expirée");
}

# on initialise le montant total de la commande
$montant_total = 0;

# connexion à la base de données
$bdd = getBD();

# Récupération des informations du client
$nom = $_SESSION['client']['nom'] ?? '';
$prenom = $_SESSION['client']['prenom'] ?? '';
$adresse = $_SESSION['client']['adresse'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>Chrono & Co.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Roboto&display=swap"
      rel="stylesheet"
    />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link
      rel="stylesheet"
      href="styles/style.css"
      type="text/css"
      media="screen"
    />
    <link
      rel="stylesheet"
      href="styles/index.css"
      type="text/css"
      media="screen"
    />
    <link
      rel="stylesheet"
      href="styles/commande.css"
      type="text/css"
      media="screen"
    />
  </head>
  <body>
    <header>
    <h1>Chrono & Co.</h1>
    </header>
    <main>
    <?php 
        if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
            echo "<p class='phrase_panier'>Votre panier ne contient aucun article.</p>";
        } else {
            echo "<h2>Récapitulatif de votre commande :</h2>";
            echo "<table border='1'>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prix unitaire</th>
                        <th>Quantité</th>
                        <th>Prix total</th>
                    </tr>";
    
            foreach ($_SESSION['panier'] as $item) {
                $id = $item['id'];
                $quantite = $item['quantite'];
    
                // Récupérer les informations de l'article depuis la base de données
                $requete = $bdd->prepare("SELECT nom, prix FROM articles WHERE id_art = ?");
                $requete->execute([$id]);
                $article = $requete->fetch();
    
                if ($article) {
                    $nom_article = $article['nom'];
                    $prix = $article['prix'];
                    $prix_total = $prix * $quantite;
                    $montant_total += $prix_total;
    
                    echo "<tr>
                            <td>" .    $id . "</td>
                            <td>" . $nom_article . "</td>
                            <td>" . $prix . " €</td>
                            <td>" . $quantite . "</td>
                            <td>" . $prix_total . " €</td>
                        </tr>";
                }
            }
    
            echo "</table>";
            echo "<p class='phrase_panier'>Montant total de la commande : <strong>" . $montant_total . " €</strong></p>";
            echo "<p> La commande sera expédiée à l'adresse suivante : <strong>" . "$prenom $nom, $adresse" . "</strong>" . "</p>";
            echo "<form action='page_paiement.php' method='POST' style='display: inline;'>";
            echo "<input type='hidden' name='token' value='" . generateToken() . "'>";
            echo "<button type='submit' class='retour_commande'>Payer</button>";
            echo "</form>";
        }
        ?>
          <a href="index.php" class="retour_commande">Retour</a>
        </main>
        <footer></footer>
      </body>
    </html>