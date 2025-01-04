<?php
// Démarrer la session
session_start();

// Inclure le fichier de configuration
require_once '../../includes/config.php';
$errors = [];

// Vérifier si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier que tous les champs nécessaires sont remplis
    if (empty($_POST['nom_user_edit']) || empty($_POST['role_user_edit']) || empty($_POST['prenom_user_edit'])) {
        // Ajouter un message d'erreur dans le tableau
        $errors[] = "Veuillez remplir tous les champs.";
    }

    // Récupérer les données du formulaire
    $id = $_POST['id_user'];
    $nom = htmlspecialchars(trim($_POST['nom_user_edit'])); // Sécuriser les données
    $role = htmlspecialchars(trim($_POST['role_user_edit']));
    $prenom = htmlspecialchars(trim($_POST['prenom_user_edit']));
    $statut = htmlspecialchars(trim($_POST['statut_user_edit']));

    // Si des erreurs existent, les enregistrer dans la session et rediriger
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ./users.php");
        exit;
    }

    try {
        // Préparer la requête pour mettre à jour les informations de l'utilisateur
        $stmt = $pdo->prepare("
            UPDATE users 
            SET prenom_user = :prenom, nom_user = :nom, role_user = :role, statut = :statut
            WHERE id = :id
        ");
        $stmt->execute([
            ':prenom' => $prenom,
            ':nom' => $nom,
            ':role' => $role,
            ':id' => $id,
            ':statut' => $statut === 'true' ? 1 : 0
        ]);

        // Vérifier si une ligne a été affectée
        if ($stmt->rowCount() === 0) {
            // Ajouter un message d'erreur dans le tableau
            $errors[] = "Aucun utilisateur trouvé ou aucune modification effectuée.";
            $_SESSION['errors'] = $errors;
            header("Location: ./users.php");
            exit;
        }

        // Ajouter un message de succès dans la session
        $_SESSION['success'] = "Utilisateur modifié avec succès.";
        header("Location: ./users.php");
        exit;
    } catch (PDOException $e) {
        // Ajouter un message d'erreur dans le tableau
        $errors[] = "Erreur lors de la modification : " . htmlspecialchars($e->getMessage());
        $_SESSION['errors'] = $errors;
        header("Location: ./users.php");
        exit;
    }
}

// Si la méthode n'est pas POST
$errors[] = "Méthode non autorisée.";
$_SESSION['errors'] = $errors;
header("Location: ./users.php");
exit;
?>
