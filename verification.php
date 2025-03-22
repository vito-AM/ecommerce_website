<?php
function verifierEtTraiterCommande($panier, $id_client) {
    $bdd = getBD();
    try {
        // Démarrer une transaction
        $bdd->beginTransaction();
        
        foreach ($panier as $item) {
            // Vérifier le stock actuel
            $sql = "SELECT quantite FROM articles WHERE id_art = ? FOR UPDATE";  // FOR UPDATE verrouille la ligne
            $stmt = $bdd->prepare($sql);
            $stmt->execute([$item['id']]);
            $article = $stmt->fetch();
            
            if ($article['quantite'] < $item['quantite']) {
                // Annuler la transaction si stock insuffisant
                $bdd->rollBack();
                return ["success" => false, "message" => "Stock insuffisant pour l'article " . $item['id']];
            }
            
            // Mettre à jour le stock
            $sql = "UPDATE articles SET quantite = quantite - ? WHERE id_art = ?";
            $stmt = $bdd->prepare($sql);
            $stmt->execute([$item['quantite'], $item['id']]);
            
            // Créer la commande
            $sql = "INSERT INTO Commandes (id_art, id_client, quantite) VALUES (?, ?, ?)";
            $stmt = $bdd->prepare($sql);
            $stmt->execute([$item['id'], $id_client, $item['quantite']]);
        }
        
        // Valider la transaction
        $bdd->commit();
        return ["success" => true, "message" => "Commande effectuée avec succès"];
        
    } catch (Exception $e) {
        $bdd->rollBack();
        return ["success" => false, "message" => "Erreur lors de la commande: " . $e->getMessage()];
    }
}
?>