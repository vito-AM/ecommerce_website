<?php
session_start();
require_once("bd.php");
require_once("stripe.php");

if (!isset($_GET['session_id'])) {
    header('Location: panier.php');
    exit();
}

try {
    $session = $stripe->checkout->sessions->retrieve($_GET['session_id']);
    if ($session->payment_status !== 'paid') {
        header('Location: panier.php');
        exit();
    }
} catch (Exception $e) {
    header('Location: panier.php');
    exit();
}

if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    header('Location: panier.php');
    exit();
}

$bdd = getBD();
$message = '';

try {
    $bdd->beginTransaction();
    $erreur = false;

    // Première étape : vérifier le stock pour tous les articles
    foreach ($_SESSION['panier'] as $item) {
        $id_art = $item['id'];
        $quantite = $item['quantite'];

        // Vérifier le stock actuel avec un verrou
        $stmt = $bdd->prepare("SELECT quantite FROM articles WHERE id_art = ? FOR UPDATE");
        $stmt->execute([$id_art]);
        $article = $stmt->fetch();

        if (!$article || $article['quantite'] < $quantite) {
            $erreur = true;
            $message = "Stock insuffisant pour certains articles.";
            break;
        }
    }

    // Si pas d'erreur de stock, procéder à la commande
    if (!$erreur) {
        foreach ($_SESSION['panier'] as $item) {
            $id_art = $item['id'];
            $quantite = $item['quantite'];
            $id_client = $_SESSION['client']['id'];

            // Insérer la commande
            $sql_insert = "INSERT INTO Commandes (id_art, id_client, quantite) VALUES (?, ?, ?)";
            $stmt_insert = $bdd->prepare($sql_insert);
            $stmt_insert->execute([$id_art, $id_client, $quantite]);

            // Mettre à jour le stock
            $sql_update = "UPDATE articles SET quantite = quantite - ? WHERE id_art = ?";
            $stmt_update = $bdd->prepare($sql_update);
            $stmt_update->execute([$quantite, $id_art]);
        }

        $bdd->commit();
        unset($_SESSION['panier']);
        $message = "Votre commande a bien été enregistrée.";
    } else {
        $bdd->rollBack();
        header('Location: panier.php?error=' . urlencode($message));
        exit();
    }
} catch (Exception $e) {
    $bdd->rollBack();
    $message = "Une erreur est survenue : " . $e->getMessage();
}
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
<body>
    <h1>Confirmation de commande</h1>
    <p class="phrase"><?php echo $message; ?></p>
    <a href="index.php" class="bouton_retour">Retour à la page d'accueil</a>
</body>
</html>