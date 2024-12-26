<?php
require_once '../../includes/config.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $venteId = intval($_GET['id']);

    try {
        // Préparer la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM ventes WHERE id = :venteId");
        $stmt->execute([':venteId' => $venteId]);

        if ($stmt->rowCount() > 0) {
            // Rediriger avec un message de succès
            header("Location: ./ventes.php?success=Vente supprimé avec succès.");
        } else {
            // Aucune Vente supprimee
            header("Location: ./ventes.php?error=Vente introuvable.");
        }
    } catch (PDOException $e) {
        // Gestion des erreurs
        header("Location: ./ventes.php?error=Erreur lors de la suppression.");
    }
} else {
    header("Location: ./ventes.php?error=ID Vente invalide.");
}
exit;
