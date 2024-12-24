<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_user'];
    $nom = $_POST['nom_user'];
    $pseudo = $_POST['pseudo_user'];
    $role = $_POST['role_user'];

    try {
        $stmt = $pdo->prepare("UPDATE users SET nom_user = :nom, pseudo_user = :pseudo, role_user = :role WHERE id = :id");
        $stmt->execute([
            ':nom' => $nom,
            ':pseudo' => $pseudo,
            ':role' => $role,
            ':id' => $id
        ]);
        header("Location: users.php?success=Utilisateur modifié avec succès.");
        exit;
    } catch (PDOException $e) {
        header("Location: users.php?error=Erreur lors de la modification : " . $e->getMessage());
        exit;
    }
}
