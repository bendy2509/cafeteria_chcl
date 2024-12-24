<?php
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom_client = trim($_POST['nom_client']);
    $type_client = trim($_POST['type_client']);
    $phone_client = trim($_POST['phone_client']);

    $errors = [];
    if (empty($nom_client)) {
        $errors[] = "Le nom du client est requis.";
    }
    if (empty($type_client)) {
        $errors[] = "Le type de client est requis.";
    }
    if (empty($phone_client) || !preg_match('/^\d{8}$/', $phone_client)) {
        $errors[] = "Un numéro de téléphone valide (8 chiffres) est requis.";
    }

    if (empty($errors)) {
        try {
            // Récupérer tous les codes clients existants
            $sql = $pdo->prepare("SELECT code_client FROM clients");
            $sql->execute();
            $result = $sql->fetchAll(PDO::FETCH_COLUMN);

            // Générer un nouveau code client
            $max_number = 0;
            foreach ($result as $code) {
                if (preg_match("/CLIENT-(\d+)/", $code, $matches)) {
                    $number = (int) $matches[1];
                    if ($number > $max_number) {
                        $max_number = $number;
                    }
                }
            }
            $new_number = $max_number + 1;
            $code_client = 'CLIENT-' . str_pad($new_number, 3, '0', STR_PAD_LEFT);

            // Insérer le nouveau client dans la base de données
            $stmt = $pdo->prepare("
                INSERT INTO clients (code_client, nom_client, type_client, phone_client)
                VALUES (:code_client, :nom_client, :type_client, :phone_client)
            ");
            $stmt->execute([
                ':code_client' => $code_client,
                ':nom_client' => $nom_client,
                ':type_client' => $type_client,
                ':phone_client' => $phone_client
            ]);

            // Redirection après succès
            header("Location: ./clients.php?success=Client ajouté avec succès.");
            exit;
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'ajout du client : " . $e->getMessage();
        }
    }

    // Gestion des erreurs
    session_start();
    $_SESSION['errors'] = $errors;
    header("Location: ./clients.php");
    exit;
}
?>
