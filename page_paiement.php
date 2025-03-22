<?php
require_once('vendor/autoload.php');
require_once('stripe.php');
require_once('bd.php');
require_once('token.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo 'Invalid request';
    exit;
}

if (!isset($_SESSION['client']) || !validateToken($_POST['token'])) {
    exit('Unauthorized access');
}

$bdd = getBD();
$line_items = [];

foreach ($_SESSION['panier'] as $item) {
    $stmt = $bdd->prepare('SELECT ID_STRIPE FROM articles WHERE id_art = ?');
    $stmt->execute([$item['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product && $product['ID_STRIPE']) {
        $line_items[] = [
            'price' => $product['ID_STRIPE'],
            'quantity' => intval($item['quantite'])
        ];
    }
}

$checkout_session = $stripe->checkout->sessions->create([
    'customer' => $_SESSION['client']['ID_STRIPE'],
    'success_url' => 'http://127.0.0.1/Marchionni/Marchionni/acheter.php?session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => 'http://127.0.0.1/Marchionni/Marchionni/commande.php',
    'mode' => 'payment',
    'automatic_tax' => ['enabled' => false],
    'line_items' => $line_items,
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);