<?php
session_start();

session_unset(); // supprime toutes les variables de la session
session_destroy(); // détruit la session

header('Location: index.php');
exit();
?>