<?php
session_start();
require_once("bd.php");
require_once("token.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    header('Location: connexion.php');
    exit();
}

// Initialiser le montant total de la commande
$montant_total = 0;

// Connexion à la base de données
$bdd = getBD();
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>Panier - Chrono & Co.</title>
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
      href="styles/panier.css"
      type="text/css"
      media="screen"
    />
  </head>
  <body>
    <header>
      <h1>Chrono & Co.</h1>
    </header>
    <main>
      <h2> Panier </h2>
    <?php
    if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
      echo "<p class='phrase_panier'>Votre panier ne contient aucun article.</p>";
  } else {
      echo "<table border='1'>
              <tr>
                  <th>ID</th>
                  <th>Nom</th>
                  <th>Prix unitaire</th>
                  <th>Quantité</th>
                  <th>Stock disponible</th>
                  <th>Prix total</th>
              </tr>";
  
      $erreur_stock = false;
  
      foreach ($_SESSION['panier'] as $item) {
          $id = $item['id'];
          $quantite = $item['quantite'];
  
          // Récupérer les informations de l'article depuis la base de données
          $requete = $bdd->prepare("SELECT nom, prix, quantite as stock FROM articles WHERE id_art = ?");
          $requete->execute([$id]);
          $article = $requete->fetch();
  
          if ($article) {
              $nom = $article['nom'];
              $prix = $article['prix'];
              $stock = $article['stock'];
              $prix_total = $prix * $quantite;
              $montant_total += $prix_total;
  
              $class_stock = $quantite > $stock ? 'stock_error' : '';
              if ($quantite > $stock) {
                  $erreur_stock = true;
              }
  
              echo "<tr class='$class_stock'>
                      <td>$id</td>
                      <td>$nom</td>
                      <td>$prix €</td>
                      <td>$quantite</td>
                      <td>$stock</td>
                      <td>$prix_total €</td>
                    </tr>";
          }
      }
  
      echo "</table>";
      echo "<p class='phrase_panier'>Montant total de la commande : <strong> $montant_total € </strong></p>";
      
      if ($erreur_stock) {
        echo "<p class='error_message'>Attention : Certains articles dépassent le stock disponible.</p>";
    } else {
      echo "<form action='commande.php' method='POST'>";
      echo "<input type='hidden' name='token' value='" . generateToken() . "'>";
      echo "<button type='submit' class='phrase_commande'>Passer la commande</button>";
      echo "</form>";
  }
}
?>

    <a href="index.php" class="retour_panier">Retour</a>
    </main>
    <footer></footer>
  </body>
</html>
