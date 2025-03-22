<?php
session_start();
require_once("bd.php");
require_once("token.php");

header('Content-Type: application/json');

// Vérification si la requête est en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Vérification du token CSRF
if (!validateToken($_POST['token'])) {
    echo json_encode([
        'success' => false, 
        'message' => "Session invalide ou expirée. Veuillez réessayer."
    ]);
    exit();
}

// Vérification des champs requis
if (!isset($_POST['mail']) || !isset($_POST['mdp']) || 
    empty($_POST['mail']) || empty($_POST['mdp'])) {
    echo json_encode([
        'success' => false, 
        'message' => "Veuillez remplir tous les champs"
    ]);
    exit();
}

$mail = $_POST['mail'];
$mdp = $_POST['mdp'];

try {
    $bdd = getBD();
    $requete = "SELECT * FROM clients WHERE mail = :mail";
    $statement = $bdd->prepare($requete);
    $statement->execute(['mail' => $mail]);
    
    $client = $statement->fetch(PDO::FETCH_ASSOC);

    if ($client && password_verify($mdp, $client['mdp'])) {
        $_SESSION['client'] = array(
            'id' => $client['id_client'],
            'nom' => $client['nom'],
            'prenom' => $client['prenom'],
            'adresse' => $client['adresse'],
            'numero' => $client['numero'],
            'mail' => $client['mail'],
            'ID_STRIPE' => $client['ID_STRIPE']

        );
        echo json_encode(['success' => true, 'message' => 'Connexion réussie']);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => "Email ou mot de passe incorrect"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => "Erreur de connexion à la base de données"
    ]);
}
?>