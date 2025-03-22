<?php
require_once("bd.php");

$bdd = getBD();
$email = $_POST['mail'];
$sql = "SELECT COUNT(*) FROM Clients WHERE mail = ?";
$req = $bdd->prepare($sql);
$req->execute([$email]);
    
header('Content-Type: application/json');
echo json_encode(['exists' => $req->fetchColumn() > 0]);
?>