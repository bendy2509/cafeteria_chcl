<?php
require_once '../../includes/config.php';

if (isset($_GET['code_plat'])) {
    $code_plat = $_GET['code_plat'];

    try {
        // Préparer la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM plats WHERE code_plat = :code_plat");
        $stmt->execute([':code_plat' => $code_plat]);

        if ($stmt->rowCount() > 0) {
            // Rediriger avec un message de succès
            header("Location: ./plats.php?success=Plat supprimé avec succès.");
        } else {
            // Aucun Plat supprime
            header("Location: ./plats.php?error=Plat introuvable.");
        }
    } catch (PDOException $e) {
        // Gestion des erreurs
        header("Location: ./plats.php?error=Erreur lors de la suppression.");
    }
} else {
    header("Location: ./plats.php?error=ID Plat invalide.");
}
exit;
