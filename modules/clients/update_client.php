<?php
// Démarrer la session
session_start();

// Inclure le fichier de configuration
require_once '../../includes/config.php';
$errors = [];

// Vérifier si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier que tous les champs nécessaires sont remplis
    if (empty($_POST['nom_client_edit']) || empty($_POST['type_client_edit']) || empty($_POST['phone_client_edit'])) {
        // Ajouter un message d'erreur dans le tableau
        $errors[] = "Veuillez remplir tous les champs.";
    }

    // Récupérer les données du formulaire
    $id = $_POST['id_client'];
    $nom = htmlspecialchars(trim($_POST['nom_client_edit'])); // Sécuriser les données
    $type = htmlspecialchars(trim($_POST['type_client_edit']));
    $phone = htmlspecialchars(trim($_POST['phone_client_edit']));

    // Si des erreurs existent, les enregistrer dans la session et rediriger
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ./clients.php");
        exit;
    }

    try {
        // Préparer la requête pour mettre à jour les informations de l'client
        $stmt = $pdo->prepare("
            UPDATE clients 
            SET phone_client = :phone_client, nom_client = :nom, type_client = :type_client 
            WHERE id = :id
        ");
        $stmt->execute([
            ':phone_client' => $phone,
            ':nom' => $nom,
            ':type_client' => $type,
            ':id' => $id
        ]);

        // Vérifier si une ligne a été affectée
        if ($stmt->rowCount() === 0) {
            // Ajouter un message d'erreur dans le tableau
            $errors[] = "Aucun client trouvé ou aucune modification effectuée.";
            $_SESSION['errors'] = $errors;
            header("Location: ./clients.php");
            exit;
        }

        // Ajouter un message de succès dans la session
        $_SESSION['success'] = "client modifié avec succès.";
        header("Location: ./clients.php");
        exit;
    } catch (PDOException $e) {
        // Ajouter un message d'erreur dans le tableau
        $errors[] = "Erreur lors de la modification : " . htmlspecialchars($e->getMessage());
        $_SESSION['errors'] = $errors;
        header("Location: ./clients.php");
        exit;
    }
}

// Si la méthode n'est pas POST
$errors[] = "Méthode non autorisée.";
$_SESSION['errors'] = $errors;
header("Location: ./clients.php");
exit;
?>
