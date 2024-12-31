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
    if ($nbre_plat > 1 || $nbre_plat < 1) {
        $errors[] = "Un seul plat est requis pour un client.";
    }

    if (empty($errors)) {
        try {
            // Vérifier si une vente existe déjà pour ce client et ce plat aujourd'hui
            $stmt = $pdo->prepare("
                SELECT COUNT(*) FROM ventes 
                WHERE code_client = :code_client
                AND DATE(date_vente) = CURDATE()
            ");
            $stmt->execute([
                ':code_client' => $client
            ]);

            if ($stmt->fetchColumn() > 0) {
                $errors[] = "Une vente a déjà été effectuée pour ce client aujourd'hui.";
            } else {
                //Verifier si le nombre de plat est insuffisant
                $stmt = $pdo->prepare("
                    SELECT quantite_plat FROM plats WHERE code_plat = :code_plat
                ");
                $stmt->execute([
                    ':code_plat' => $code_plat
                ]);
                $quantite = $stmt->fetchColumn();
                if ($quantite < $nbre_plat) {
                    $errors[] = "Le nombre de plats est insuffisant.";
                    $_SESSION['errors'] = $errors;
                    header("Location: ./ventes.php");
                    exit;
                }

                // Diminuer le stock du plat
                $stmt = $pdo->prepare("
                    UPDATE plats SET quantite_plat = quantite_plat - :nbre_plat
                    WHERE code_plat = :code_plat
                ");
                $stmt->execute([
                    ':nbre_plat' => $nbre_plat,
                    ':code_plat' => $code_plat
                ]);

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

                // Ajouter un message dans la session
                // Ajout un message dans la session
                $_SESSION['success'] = "Vente ajoutée avec succès.";
                // Redirection après succès
                header("Location: ./ventes.php");
                exit;
            }
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