<?php
require_once '../../includes/config.php';
session_start();

// Initialiser le tableau des erreurs
if (!isset($_SESSION['errors'])) {
    $_SESSION['errors'] = [];
}

if (isset($_GET['code_plat'])) {
    $code_plat = $_GET['code_plat'];

    try {
        //Verifier si le plat ne se trouve pas dans une vente
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ventes WHERE code_plat = :code_plat");
        $stmt->execute([':code_plat' => $code_plat]);

        if ($stmt->fetchColumn() > 0) {
            // Stocker le message d'erreur dans la session
            $_SESSION['errors'][] = "Impossible de supprimer ce plat. Il est déjà dans une vente.";
            header("Location: ./plats.php");
            exit;
        }
        // Préparer la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM plats WHERE code_plat = :code_plat");
        $stmt->execute([':code_plat' => $code_plat]);

        if ($stmt->rowCount() > 0) {
            // Rediriger avec un message de succès
            header("Location: ./plats.php?success=Plat supprimé avec succès.");
        } else {
            // Stocker le message d'erreur dans la session
            $_SESSION['errors'][] = "Plat introuvable.";
            header("Location: ./plats.php");
        }
    } catch (PDOException $e) {
        // Stocker le message d'erreur dans la session
        $_SESSION['errors'][] = "Erreur lors de la suppression.";
        header("Location: ./plats.php");
    }
} else {
    // Stocker le message d'erreur dans la session
    $_SESSION['errors'][] = "ID Plat invalide.";
    header("Location: ./plats.php");
}
exit;
