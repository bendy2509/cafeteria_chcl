<?php
// Démarrer la session
session_start();

// Inclure le fichier de configuration
require_once '../../includes/config.php';
$errors = [];

// Vérifier si la méthode de la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier que tous les champs nécessaires sont remplis
    if (empty($_POST['nom_plat_edit']) || empty($_POST['cuisson_plat_edit'])) {
        // Ajouter un message d'erreur dans le tableau
        $errors[] = "Veuillez remplir tous les champs.";
    }

    // Récupérer les données du formulaire
    $id = $_POST['id_plat'];
    $nom = htmlspecialchars(trim($_POST['nom_plat_edit'])); // Sécuriser les données
    $cuisson = htmlspecialchars(trim($_POST['cuisson_plat_edit']));
    $quantite = htmlspecialchars(trim($_POST['quantite_plat_edit']));
    $prix = $_POST['prix_plat_edit'];

    // Si des erreurs existent, les enregistrer dans la session et rediriger
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: ./plats.php");
        exit;
    }

    try {
        // Préparer la requête pour mettre à jour les informations de l'plat
        $stmt = $pdo->prepare("
            UPDATE plats 
            SET quantite_plat = :quantite_plat, nom_plat = :nom, cuisson_plat = :cuisson_plat 
            WHERE code_plat = :id
        ");
        $stmt->execute([
            ':quantite_plat' => $quantite,
            ':nom' => $nom,
            ':cuisson_plat' => $cuisson,
            ':id' => $id
        ]);

        // Vérifier si une ligne a été affectée
        if ($stmt->rowCount() === 0) {
            // Ajouter un message d'erreur dans le tableau
            $errors[] = "Aucun plat trouvé ou aucune modification effectuée.";
            $_SESSION['errors'] = $errors;
            header("Location: ./plats.php");
            exit;
        }

        // Ajouter un message de succès dans la session
        $_SESSION['success'] = "plat modifié avec succès.";
        header("Location: ./plats.php");
        exit;
    } catch (PDOException $e) {
        // Ajouter un message d'erreur dans le tableau
        $errors[] = "Erreur lors de la modification : " . htmlspecialchars($e->getMessage());
        $_SESSION['errors'] = $errors;
        header("Location: ./plats.php");
        exit;
    }
}

// Si la méthode n'est pas POST
$errors[] = "Méthode non autorisée.";
$_SESSION['errors'] = $errors;
header("Location: ./plats.php");
exit;
?>
