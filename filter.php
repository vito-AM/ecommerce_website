<?php
function analyzeMessage($message) {
    // Configuration
    $api_url = 'http://127.0.0.1:5000/analyze';
    
    // Préparation de la requête
    $data = json_encode(['message' => $message]);
    
    // Configuration de cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);
    
    // Exécution de la requête
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // Vérification de la réponse
    if ($http_code === 200) {
        $result = json_decode($response, true);
        return $result['is_appropriate'] ?? false;
    }
    
    // En cas d'erreur, on accepte le message par défaut
    return true;
}
?>