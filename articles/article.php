<?php
session_start();
require_once("../bd.php");
require_once("../token.php");
$bdd = getBD();

$id_art = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$req = $bdd->prepare("SELECT * FROM articles WHERE id_art = :id_art");
$req->execute(['id_art' => $id_art]);
$article = $req->fetch();
$req->closeCursor();

if (!$article) {
    header("Location: ../index.php");
    exit();
}

$nom = $article["nom"];
$description = $article["description"];
$url_photo = $article["url_photo"];
$prix = $article["prix"];
$quantite_stock = $article["quantite"];
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <title><?php echo($nom); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" href="../styles/index.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="../styles/style.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="../styles/articles.css" type="text/css" media="screen" />
  </head>
  <body>
    <header>
      <h1>Chrono & Co.</h1>
    </header>
    <main>
      <div class="conteneur_image">
        <img src="<?php echo($url_photo); ?>" alt="<?php echo("image de l'article ". $nom); ?>" />
        <div class="conteneur_texte">
          <h2><?php echo($nom); ?></h2>
          <p><?php echo($description); ?></p></br>
          <p>Prix : <?php echo($prix); ?> €</p>
          <p>Quantité en stock : <?php echo($quantite_stock); ?></p>
          
          <?php if (isset($_SESSION['client'])) : ?>
          <form action="ajouter.php" method="POST">
            <input type="hidden" name="token" value="<?php echo generateToken(); ?>">
            <input type="hidden" name="id_article" value="<?php echo $id_art; ?>">
            <label for="quantite">Quantité :</label>
            <input type="number" id="quantite" name="quantite" min="1" max="<?php echo $quantite_stock; ?>" value="1" required>
            <input type="submit" value="Ajoutez à votre panier" class="ajouter_panier">  
          </form>
          <?php endif; ?>

          <a href="../index.php" class="retour">Retour</a>
        </div>
      </div>
    </main>
  </body>
</html>