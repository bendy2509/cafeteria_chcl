<?php
session_start();

require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = trim($_POST['client']);
    $code_plat = trim($_POST['plat']);
    $nbre_plat = trim($_POST['nbre_plat']);

    $errors = [];
    if (empty($client)) {
        $errors[] = "Le nom du client est requis.";
    }
    if (!preg_match('/^[+]?[0-9]+$/', $nbre_plat)) {
        $errors[] = "Pour le nombre de plat un nombre entier est requis.";
    }

    if (empty($errors)) {
        try {
            // Insérer la nouvelle vente dans la base de données
            $stmt = $pdo->prepare("
                INSERT INTO ventes (code_plat, code_client, nbre_plat)
                VALUES (:code_plat, :code_client, :nbre_plat)
            ");
            $stmt->execute([
                ':code_plat' => $code_plat,
                ':code_client' => $client,
                ':nbre_plat' => $nbre_plat
            ]);

            //Ajout un message dans la session
            $_SESSION['success'] = "Vente ajouté avec succès.";

            // Redirection après succès
            header("Location: ./ventes.php");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'ajout de la vente : " . $e->getMessage();
        }
    }

    // Gestion des erreurs
    $_SESSION['errors'] = $errors;
    header("Location: ./ventes.php");
    exit;
}
?>