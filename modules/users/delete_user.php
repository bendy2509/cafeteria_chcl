<?php
require_once '../../includes/config.php';


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $userId = intval($_GET['id']);

    try {
        // Préparer la requête de suppression
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);

        if ($stmt->rowCount() > 0) {
            // Rediriger avec un message de succès
            header("Location: ./users.php?success=Utilisateur supprimé avec succès.");
        } else {
            // Aucun utilisateur supprime
            header("Location: ./users.php?error=Utilisateur introuvable.");
        }
    } catch (PDOException $e) {
        // Gestion des erreurs
        header("Location: ./users.php?error=Erreur lors de la suppression.");
    }
} else {
    header("Location: ./users.php?error=ID utilisateur invalide.");
}
exit;
