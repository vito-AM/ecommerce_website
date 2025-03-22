<?php
require_once("bd.php");
require_once('vendor/autoload.php');
require_once('stripe.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

function verifierExistenceEmail($mail) {
    $bdd = getBD();
    $sql = "SELECT COUNT(*) FROM Clients WHERE mail = ?";
    $req = $bdd->prepare($sql);
    $req->execute([$mail]);
    return $req->fetchColumn() > 0;
}

function verifierExistenceNumero($numero) {
    $bdd = getBD();
    $sql = "SELECT COUNT(*) FROM Clients WHERE numero = ?";
    $req = $bdd->prepare($sql);
    $req->execute([$numero]);
    return $req->fetchColumn() > 0;
}

function enregistrer($nom, $prenom, $adresse, $numero, $mail, $mdp, $stripeId) {
    $bdd = getBD();
    $mdphash = password_hash($mdp, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Clients (nom, prenom, adresse, numero, mail, mdp, ID_STRIPE) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $req = $bdd->prepare($sql);
    return $req->execute([$nom, $prenom, $adresse, $numero, $mail, $mdphash, $stripeId]);
}

if (!isset($_POST['n']) || empty($_POST['n']) ||
    !isset($_POST['p']) || empty($_POST['p']) ||
    !isset($_POST['adr']) || empty($_POST['adr']) ||
    !isset($_POST['num']) || empty($_POST['num']) ||
    !isset($_POST['mail']) || empty($_POST['mail']) ||
    !isset($_POST['mdp1']) || empty($_POST['mdp1']) ||
    !isset($_POST['mdp2']) || empty($_POST['mdp2'])) {
    
    echo json_encode(['success' => false, 'message' => 'Tous les champs sont obligatoires']);
    exit();
}

$n = $_POST['n'];
$p = $_POST['p'];
$adr = $_POST['adr'];
$num = $_POST['num'];
$mail = $_POST['mail'];
$mdp = $_POST['mdp1'];

if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => "L'adresse e-mail n'est pas valide"]);
    exit();
}

if (verifierExistenceNumero($num)) {
    echo json_encode(['success' => false, 'message' => 'Un compte avec ce numéro de téléphone existe déjà']);
    exit();
}

if ($_POST['mdp1'] !== $_POST['mdp2']) {
    echo json_encode(['success' => false, 'message' => 'Les mots de passe ne correspondent pas']);
    exit();
}

try {
    // Création du client Stripe
    $customer = $stripe->customers->create([
        'name' => $n . ' ' . $p,
        'email' => $mail,
        'phone' => $num,
        'address' => [
            'line1' => $adr
        ]
    ]);

    // Enregistrement dans la base de données avec l'ID Stripe
    if (enregistrer($n, $p, $adr, $num, $mail, $mdp, $customer->id)) {
        session_start();
        
        $bdd = getBD();
        $sql = "SELECT id_client, nom, prenom FROM Clients WHERE mail = ?";
        $req = $bdd->prepare($sql);
        $req->execute([$mail]);
        $client = $req->fetch();
        
        $_SESSION['client'] = [
            'id' => $client['id_client'], 
            'nom' => $client['nom'],
            'prenom' => $client['prenom'],
            'mail' => $mail,
            'stripe_id' => $customer->id
        ];
        
        echo json_encode(['success' => true, 'message' => 'Compte créé avec succès']);
    } else {
        // En cas d'échec de l'enregistrement en base de données, supprimer le client Stripe
        $stripe->customers->delete($customer->id);
        echo json_encode(['success' => false, 'message' => "Erreur lors de l'enregistrement"]);
    }
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode(['success' => false, 'message' => "Erreur lors de la création du compte: " . $e->getMessage()]);
}
?>