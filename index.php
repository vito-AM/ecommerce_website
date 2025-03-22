<?php
session_start()
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
    <link rel="stylesheet" href="styles/chat.css" type="text/css" media="screen" />
    <link
      rel="stylesheet"
      href="styles/index.css"
      type="text/css"
      media="screen"
    />
  </head>
  <body>
    <header>
      <h1>Chrono & Co.</h1>
      <?php
      if (!isset($_SESSION['client'])) {
        // Utilisateur non connecté
        echo '<a href="nouveau.php" class="nouveau">Nouveau Client</a>';
        echo '<a href="connexion.php" class="connexion">Se connecter</a>';
      } elseif(isset($_SESSION['client'])) {
        // Utilisateur connecté
        echo '<p class="bonjour">Bonjour ' . $_SESSION['client']['prenom'] . ' ' . $_SESSION['client']['nom'] . '</p>';
        echo '<a href="deconnexion.php" class="deconnexion">Se déconnecter</a>';
        echo '<br><a href="panier.php" class="panier">Voir mon panier</a></br>';
        echo '<br><a href="historique.php" class="historique">Historique des commandes</a>';
      }
      ?>
    </header>
    <main>
      <table>
        <tr>
          <th>Nom</th>
          <th>Identifiant</th>
          <th>Quantité en stock</th>
          <th>Prix (€)</th>
        </tr>
<?php
      require_once("bd.php") ;
      $bdd = getBD();
      $rep = $bdd->query('select*from articles');
      while ($ligne = $rep->fetch()){
          echo "<tr>";
            echo "<td> <a href='articles/article.php?id=" . $ligne['id_art'] . "'>" . $ligne['nom'] . "</a></td>";
            echo "<td>" . $ligne["id_art"] . "</td>";
            echo "<td>" . $ligne["quantite"] . "</td>";
            echo "<td>" . $ligne["prix"] . "</td>";
            echo "</tr>";
        }
        $rep->closeCursor();
?>
      </table>
      <a href="contact/contact.html" class="contact">Contactez-moi</a>
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php
    require_once("chat_token.php");
    echo '<script>const CHAT_CSRF_TOKEN = "' . generateChatToken() . '";</script>';
    ?>
    <script src="chat.js"></script>
  </body>
</html>
