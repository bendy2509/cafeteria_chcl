<?php
session_start();
require_once '../../includes/config.php';

if (isset($_GET['code_client'])) {
    $code_client = $_GET['code_client'];

    try {
        // Vérifier si le client ne se trouve pas dans une vente
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ventes WHERE code_client = :code_client");
        $stmt->execute([':code_client' => $code_client]);

        if ($stmt->fetchColumn() > 0) {
            $_SESSION['errors'][] = "Impossible de supprimer ce client. Il est déjà dans une vente.";
            header("Location: ./clients.php");
            exit;
        }
        // Préparer la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM clients WHERE code_client = :code_client");
        $stmt->execute([':code_client' => $code_client]);

        if ($stmt->rowCount() > 0) {
            // Rediriger avec un message de succès
            $_SESSION['success'] = "Client supprimé avec succès.";
            header("Location: ./clients.php");
        } else {
            // Aucun client supprimé
            $_SESSION['errors'][] = "Client introuvable.";
            header("Location: ./clients.php");
        }
    } catch (PDOException $e) {
        // Gestion des erreurs
        $_SESSION['errors'][] = "Erreur lors de la suppression.";
        header("Location: ./clients.php");
    }
} else {
    $_SESSION['errors'][] = "Code Client invalide.";
    header("Location: ./clients.php");
}
exit;
