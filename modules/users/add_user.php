<?php
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_user = trim($_POST['nom_user']);
    $role_user = trim($_POST['role_user']);
    $prenom_user = trim($_POST['prenom_user']);
    $pseudo_user = trim($_POST['pseudo_user']);
    $email_user = trim($_POST['email_user']);
    $password_user = trim($_POST['password_user']);
    $password_user_confirm = trim($_POST['password_user1']);

    $errors = [];

    // Validation des champs obligatoires
    if (empty($nom_user) || empty($prenom_user) || empty($email_user) || empty($role_user) || empty($password_user) || empty($pseudo_user)) {
        $errors[] = "Tous les champs sont requis.";
    }

    // Validation de l'email
    if (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'email est incorrect.";
    }

    // Validation du mot de passe
    if ($password_user !== $password_user_confirm) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }

    if (empty($errors)) {
        try {
            // Vérification de l'unicité de l'email et du pseudo
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email_user = :email_user OR pseudo_user = :pseudo_user");
            $stmt->execute([
                ':email_user' => $email_user,
                ':pseudo_user' => $pseudo_user
            ]);
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $errors[] = "L'email ou le pseudo existe déjà.";
            } else {
                // Insertion des données dans la base
                $stmt = $pdo->prepare("
                    INSERT INTO users (nom_user, role_user, prenom_user, pseudo_user, email_user, password_user) 
                    VALUES (:nom_user, :role_user, :prenom_user, :pseudo_user, :email_user, :password_user)
                ");
                $stmt->execute([
                    ':nom_user' => $nom_user,
                    ':role_user' => $role_user,
                    ':prenom_user' => $prenom_user,
                    ':pseudo_user' => $pseudo_user,
                    ':email_user' => $email_user,
                    ':password_user' => password_hash($password_user, PASSWORD_DEFAULT)
                ]);

                // Redirection après succès
                header("Location: ./users.php?success=Enregistrement réussi.");
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'ajout du plat : " . $e->getMessage();
        }
    }

    // Gestion des erreurs
    session_start();
    $_SESSION['errors'] = $errors;
    header("Location: ./users.php");
    exit;
}
?>