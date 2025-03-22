<?php
session_start();
require_once("../bd.php");
require_once("../token.php");

// Vérification du token CSRF
if (!validateToken($_POST['token'])) {
    $_SESSION['error'] = "Session invalide ou expirée. Veuillez réessayer.";
    header('Location: panier.php');
    exit();
}


# on vérifie si l'utilisateur est connecté
if (!isset($_SESSION['client'])) {
    header('Location: ../connexion.php');
    exit();
}

# on vérifie si les données nécessaires sont présentes
if (!isset($_POST['id_article']) || !isset($_POST['quantite'])) {
    header('Location: ../index.php');
    exit();
}

$id_article = $_POST['id_article'];
$quantite = $_POST['quantite'];

# on vérifie que la quantité est positive
if ($quantite <= 0) {
    header('Location: article.php?id=' . $id_article); // Chemin corrigé
    exit();
}
try {
    $bdd = getBD();
    
    # on vérifie le stock disponible
    $stmt = $bdd->prepare("SELECT quantite FROM articles WHERE id_art = ?");
    $stmt->execute([$id_article]);
    $article = $stmt->fetch();

    if (!$article) {
        header('Location: ../index.php');
        exit();
    }

    # on calcule la quantité totale dans le panier pour cet article
    $quantite_panier = 0;
    if (isset($_SESSION['panier'])) {
        foreach ($_SESSION['panier'] as $item) {
            if ($item['id'] == $id_article) {
                $quantite_panier += $item['quantite'];
            }
        }
    }

    # on vérifie si la quantité totale ne dépasse pas le stock
    if ($quantite_panier + $quantite > $article['quantite']) {
        header('Location: article.php?id=' . $id_article . '&error=stock_insuffisant&stock=' . $article['quantite']); // Chemin corrigé
        exit();
    }

    # Si le panier n'existe pas, le créer
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = array();
    }

    # Ajouter l'article au panier
    $_SESSION['panier'][] = array(
        'id' => $id_article,
        'quantite' => $quantite
    );

    header('Location: ../panier.php');
    exit();

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>