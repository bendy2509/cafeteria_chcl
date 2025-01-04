<?php
// Inclus le fichier de configuration
require_once '../includes/config.php';

// Initialisation des variables
$error = null;
$ventes = [];
$totalVentes = 0;

// Vérifier si la méthode est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs des champs
    $date_debut = $_POST['date_debut'] ?? null;
    $date_fin = $_POST['date_fin'] ?? null;

    // Vérifier si les dates sont fournies
    if (empty($date_debut) || empty($date_fin)) {
        $error = "Les dates de début et de fin sont requises.";
    } else {
        try {
            // Préparation de la requête pour récupérer les ventes
            $stmt = $pdo->prepare("
                SELECT vente.id, vente.code_client, vente.code_plat, vente.nbre_plat, plats.nom_plat, plats.prix_plat, 
                       clients.nom_client, vente.date_vente
                FROM ventes vente
                LEFT JOIN plats ON vente.code_plat = plats.code_plat
                LEFT JOIN clients ON vente.code_client = clients.code_client
                WHERE vente.date_vente BETWEEN :date_debut AND :date_fin
            ");
            $stmt->execute([
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin
            ]);
            $ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calcul du total des ventes
            foreach ($ventes as $vente) {
                $totalVentes += $vente['nbre_plat'] * $vente['prix_plat'];
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport des Ventes</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-extrabold text-center text-cyan-800 mb-6">Rapport des Ventes</h1>
        <h2 class="text-xl text-center text-gray-600 mb-4">Du <?= htmlspecialchars($date_debut ?? ''); ?> au <?= htmlspecialchars($date_fin ?? ''); ?></h2>

        <?php if (empty($ventes)): ?>
            <p class="text-center text-lg text-red-500">Aucune vente trouvée pour la période sélectionnée.</p>
        <?php else: ?>
            <div class="overflow-hidden bg-white shadow-xl rounded-lg p-8">
                <table class="min-w-full text-left table-auto border">
                    <thead class="bg-[#15616D] text-white">
                        <tr>
                            <th class="px-8 py-4 text-lg font-semibold">Code Vente</th>
                            <th class="px-8 py-4 text-lg font-semibold">Client</th>
                            <th class="px-8 py-4 text-lg font-semibold">Plat</th>
                            <th class="px-8 py-4 text-lg font-semibold">Prix Unitaire (HTG)</th>
                            <th class="px-8 py-4 text-lg font-semibold">Quantité</th>
                            <th class="px-8 py-4 text-lg font-semibold">Total (HTG)</th>
                            <th class="px-8 py-4 text-lg font-semibold">Date</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php foreach ($ventes as $vente): ?>
                            <tr class="border border-gray-200 hover:bg-gray-100 transition-all duration-300">
                                <td class="px-8 py-4"><?= htmlspecialchars($vente['id']); ?></td>
                                <td class="px-8 py-4"><?= htmlspecialchars($vente['nom_client']); ?></td>
                                <td class="px-8 py-4"><?= htmlspecialchars($vente['nom_plat']); ?></td>
                                <td class="px-8 py-4"><?= number_format($vente['prix_plat'], 2); ?></td>
                                <td class="px-8 py-4"><?= htmlspecialchars($vente['nbre_plat']); ?></td>
                                <td class="px-8 py-4"><?= number_format($vente['nbre_plat'] * $vente['prix_plat'], 2); ?></td>
                                <td class="px-8 py-4"><?= htmlspecialchars($vente['date_vente']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="text-white font-semibold">
                            <td colspan="3" class="px-8 py-4"></td>
                            <td colspan="1" class="bg-[#15616D] px-8 py-4 text-right">Total des Ventes (HTG)</td>
                            <td colspan="2" class="bg-[#15616D] px-8 py-4 text-right"><?= number_format($totalVentes, 2); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Formulaire pour générer le PDF -->
            <form method="POST" action="generate_pdf.php" target="_blank" class="mt-6 text-center">
                <input type="hidden" name="date_debut" value="<?= htmlspecialchars($date_debut); ?>">
                <input type="hidden" name="date_fin" value="<?= htmlspecialchars($date_fin); ?>">
                <button type="submit" class="bg-[#15616D] text-white py-3 px-8 rounded-full hover:bg-teal-700 transition duration-300">Générer le PDF</button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>
