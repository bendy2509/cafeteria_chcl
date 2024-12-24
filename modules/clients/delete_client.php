<?php
require_once '../../includes/config.php';

if (isset($_GET['code_client'])) {
    $code_client = $_GET['code_client'];

    try {
        // Préparer la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM clients WHERE code_client = :code_client");
        $stmt->execute([':code_client' => $code_client]);

        if ($stmt->rowCount() > 0) {
            // Rediriger avec un message de succès
            header("Location: ./clients.php?success=Client supprimé avec succès.");
        } else {
            // Aucun client supprime
            header("Location: ./clients.php?error=Client introuvable.");
        }
    } catch (PDOException $e) {
        // Gestion des erreurs
        header("Location: ./clients.php?error=Erreur lors de la suppression.");
    }
} else {
    header("Location: ./clients.php?error=Code Client invalide.");
}
exit;
