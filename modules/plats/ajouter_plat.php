<?php
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_plat = trim($_POST['nom_plat']);
    $cuisson_plat = trim($_POST['cuisson_plat']);
    $prix_plat = trim($_POST['prix_plat']);
    $quantite_plat = trim($_POST['quantite_plat']);

    $errors = [];
    if (empty($nom_plat)) {
        $errors[] = "Le nom du plat est requis.";
    }
    if (empty($cuisson_plat)) {
        $errors[] = "Le cuisson du plat est requis.";
    }
    if (!preg_match('/^[+]?[0-9]*\.?[0-9]+$/', $prix_plat)) {
        $errors[] = "Pour le prix du plat un nombre est requis.";
    }
    if (!preg_match('/^[+]?[0-9]+$/', $quantite_plat)) {
        $errors[] = "Pour le nombre de plat un nombre entier est requis.";
    }

    if (empty($errors)) {
        try {
            // Récupérer tous les codes plats existants
            $sql = $pdo->prepare("SELECT code_plat FROM plats");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_COLUMN);

            // Générer un nouveau code plat
            $max_number = 0;
            foreach ($result as $code) {
                if (preg_match("/PLAT-(\d+)/", $code, $matches)) {
                    $number = (int) $matches[1];
                    if ($number > $max_number) {
                        $max_number = $number;
                    }
                }
            }
            $new_number = $max_number + 1;
            $code_plat = 'PLAT-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);

            // Insérer le nouveau plat dans la base de données
            $stmt = $pdo->prepare("
                INSERT INTO plats (code_plat, nom_plat, cuisson_plat, prix_plat, quantite_plat)
                VALUES (:code_plat, :nom_plat, :cuisson_plat, :prix_plat, :quantite_plat)
            ");
            $stmt->execute([
                ':code_plat' => $code_plat,
                ':nom_plat' => $nom_plat,
                ':cuisson_plat' => $cuisson_plat,
                ':prix_plat' => $prix_plat,
                ':quantite_plat' => $quantite_plat
            ]);

            // Redirection après succès
            header("Location: ./plats.php?success=Plat ajouté avec succès.");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'ajout du plat : " . $e->getMessage();
        }
    }

    // Gestion des erreurs
    session_start();
    $_SESSION['errors'] = $errors;
    header("Location: ./plats.php");
    exit;
}
?>
