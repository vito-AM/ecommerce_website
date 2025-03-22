<?php
function getBD() {
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=chrono_co;charset=utf8', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $bdd;
    } catch (PDOException $exception) {
        die("Erreur de connexion à la base de données : " . $exception->getMessage());
    }
}
