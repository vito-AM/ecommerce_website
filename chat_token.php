<?php
session_start();

function generateChatToken() {
    if (empty($_SESSION['chat_csrf_token'])) {
        $_SESSION['chat_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['chat_csrf_token'];
}

function validateChatToken($token) {
    if (empty($_SESSION['chat_csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['chat_csrf_token'], $token);
}
?>