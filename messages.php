<?php
session_start();
require_once("bd.php");
require_once("chat_token.php");
header('Content-Type: application/json');

try {
    $bdd = getBD();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_SESSION['client']) || !isset($_SESSION['client']['id'])) {
            echo json_encode([
                'success' => false, 
                'error' => 'Vous devez être connecté pour envoyer un message'
            ]);
            exit;
        }

        // Vérification du token CSRF
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!validateChatToken($csrfToken)) {
            echo json_encode([
                'success' => false,
                'error' => 'Erreur CSRF détectée.'
            ]);
            exit;
        }

        $id_client = $_SESSION['client']['id'];
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';
        
        if (empty($message)) {
            echo json_encode([
                'success' => false, 
                'error' => 'Le message ne peut pas être vide'
            ]);
            exit;
        }
        
        if (strlen($message) > 256) {
            echo json_encode([
                'success' => false, 
                'error' => 'Le message ne peut pas dépasser 256 caractères'
            ]);
            exit;
        }
        
        // Analyse du message avec Flask
        $flask_url = 'http://127.0.0.1:5000/analyze';
        $ch = curl_init($flask_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS => json_encode(['message' => $message])
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200) {
            echo json_encode([
                'success' => false, 
                'error' => 'Erreur lors de l\'analyse du message'
            ]);
            exit;
        }

        $analysis = json_decode($response, true);
        if (!$analysis['is_appropriate']) {
            echo json_encode([
                'success' => false, 
                'error' => 'Message inapproprié détecté. Veuillez modérer votre langage.'
            ]);
            exit;
        }
        
        // Insertion du message
        $stmt = $bdd->prepare('INSERT INTO messages (id_client, message) VALUES (?, ?)');
        $stmt->execute([$id_client, $message]);
        
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Suppression des messages de plus de 10 minutes
        $bdd->query('DELETE FROM messages WHERE timestamp < DATE_SUB(NOW(), INTERVAL 10 MINUTE)');
        
        $stmt = $bdd->prepare('SELECT m.*, c.prenom, c.nom 
                              FROM messages m 
                              JOIN clients c ON m.id_client = c.id_client 
                              ORDER BY m.timestamp DESC 
                              LIMIT 50');
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'messages' => $messages]);
        exit;
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'Une erreur technique est survenue. Veuillez réessayer.',
    ]);
    exit;
}
?>