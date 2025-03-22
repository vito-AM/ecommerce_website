<?php
require_once('vendor/autoload.php');

// Clés d'API Stripe
define('STRIPE_SECRET_KEY', 'sk_test_51QQTzQJQlT88Kz0tTYDFDyDayvfivPQ7xSMS7KAYTbNWUm95hi7enIpcgR8FZCNri5epKzdjbZodev2nGdfLEcRF00ugR2dOa8');
define('STRIPE_PUBLIC_KEY', 'pk_test_51QQTzQJQlT88Kz0t2VelLfztUrtTIydFPaP8YvkcJWEWz7hGukupddVVRNEDYCuOjIRZOxfCodizFILbtDafKIPV00mDDAIt8i');

// Initialisation de l'objet Stripe
$stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
?>