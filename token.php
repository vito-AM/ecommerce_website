<?php
function generateToken() {
    // Générer un nouveau token à chaque fois
    $token = bin2hex(random_bytes(32));
    
    // Stocker le token avec un timestamp d'expiration (par exemple 1 heure)
    $_SESSION['tokens'][] = [
        'value' => $token,
        'expires' => time() + 3600
    ];
    
    // Nettoyer les vieux tokens
    cleanExpiredTokens();
    
    return $token;
}

function validateToken($token) {
    if (empty($_SESSION['tokens']) || empty($token)) {
        return false;
    }
    
    foreach ($_SESSION['tokens'] as $key => $stored) {
        if (hash_equals($stored['value'], $token)) {
            // Token trouvé et valide
            if ($stored['expires'] > time()) {
                // Supprimer le token utilisé
                unset($_SESSION['tokens'][$key]);
                return true;
            }
            // Token expiré
            unset($_SESSION['tokens'][$key]);
            return false;
        }
    }
    
    return false;
}

function cleanExpiredTokens() {
    if (!empty($_SESSION['tokens'])) {
        foreach ($_SESSION['tokens'] as $key => $token) {
            if ($token['expires'] <= time()) {
                unset($_SESSION['tokens'][$key]);
            }
        }
    }
}