<?php
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
    $client = htmlspecialchars(trim($_POST['client']));
    $plat = htmlspecialchars(trim($_POST['plat']));
    $nbre_plat = filter_var($_POST['nbre_plat'], FILTER_VALIDATE_INT);

    if ($id && $client && $plat && $nbre_plat) {
        $query = "UPDATE ventes SET code_client = :client, code_plat = :plat, nbre_plat = :nbre_plat WHERE id = :id";
        $stmt = $pdo->prepare($query);
        try {
            $stmt->execute([
                ':client' => $client,
                ':plat' => $plat,
                ':nbre_plat' => $nbre_plat,
                ':id' => $id,
            ]);
            header('Location: ./ventes.php?message=' . urlencode('Vente modifiée avec succès'));
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage(), 0); // Log l'erreur pour le débogage
            header('Location: ./ventes.php?error=' . urlencode('Erreur lors de la modification de la vente'));
            exit;
        }
    } else {
        header('Location: ./ventes.php?error=' . urlencode('Veuillez remplir tous les champs correctement'));
        exit;
    }
}
?>
