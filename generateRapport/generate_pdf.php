<?php
// Inclus le fichier de configuration
require_once '../includes/config.php';

// Vérifier si les paramètres sont envoyés
if (isset($_POST['date_debut']) && isset($_POST['date_fin'])) {
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    // Initialisation des variables
    $ventes = [];
    $totalVentes = 0;

    // Préparation de la requête pour récupérer les ventes
    try {
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
        echo "Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage());
    }

    // Inclure la bibliothèque html2pdf et générer le PDF
    require_once '../vendor/autoload.php';
    ob_start();

    // Le contenu HTML à convertir en PDF
    ?>
    <html>

    <head>
        <title>Rapport des Ventes</title>
        <style>
            body {
                font-family: 'Arial', sans-serif;
                margin: 20px;
                background-color: #f4f4f4;
                color: #333;
            }

            h1,
            h2 {
                text-align: center;
                margin-bottom: 20px;
            }

            h1 {
                font-size: 24px;
                font-weight: bold;
            }

            h2 {
                font-size: 18px;
                font-weight: normal;
                color: black;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 30px;
                background-color: #fff;
                border-radius: 8px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }

            th,
            td {
                padding: 12px 15px;
                border: 1px solid #ddd;
                text-align: center;
            }

            th {
                background-color: #f1f1f1;
                font-weight: bold;
            }

            tr:nth-child(even) {
                background-color: #fafafa;
            }

            tr:hover {
                background-color: #f0f0f0;
            }

            .total {
                font-weight: bold;
                background-color: #f1f1f1;
                color: #333;
            }

            p {
                text-align: center;
                font-size: 16px;
                color: #666;
                margin-top: 20px;
            }
        </style>
    </head>

    <body>
        <h1>Rapport des Ventes</h1>
        <h2>Du <?= htmlspecialchars($date_debut); ?> au <?= htmlspecialchars($date_fin); ?></h2>
        <?php if (empty($ventes)): ?>
            <p>Aucune vente trouvée pour la période sélectionnée.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Code Vente</th>
                        <th>Client</th>
                        <th>Plat</th>
                        <th>Prix Unitaire (HTG)</th>
                        <th>Quantité</th>
                        <th>Total (HTG)</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventes as $vente): ?>
                        <tr>
                            <td><?= htmlspecialchars($vente['id']); ?></td>
                            <td><?= htmlspecialchars($vente['nom_client']); ?></td>
                            <td><?= htmlspecialchars($vente['nom_plat']); ?></td>
                            <td><?= number_format($vente['prix_plat'], 2); ?></td>
                            <td><?= htmlspecialchars($vente['nbre_plat']); ?></td>
                            <td><?= number_format($vente['nbre_plat'] * $vente['prix_plat'], 2); ?></td>
                            <td><?= htmlspecialchars($vente['date_vente']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total">
                        <td colspan="5">Total des Ventes (HTG)</td>
                        <td colspan="2"><?= number_format($totalVentes, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        <?php endif; ?>
    </body>

    </html>
    <?php
    $html = ob_get_clean(); // Récupérer le contenu tamponné

    // Initialiser html2pdf
    try {
        $pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'fr');
        $pdf->writeHTML($html);
        $pdf->output("rapport_ventes_{$date_debut}_{$date_fin}.pdf");
    } catch (Html2PdfException $e) {
        echo $e;
    }
}
?>
