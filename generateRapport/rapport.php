<?php
// Inclus le fichier de configuration
require_once '../includes/config.php';

// Initialisation des variables
$error = null;
$ventes = [];

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
            $stmt = $pdo->prepare("SELECT vente.id, vente.code_client, vente.code_plat, vente.nbre_plat, 
                                   plats.nom_plat, clients.nom_client, vente.date_vente
                                   FROM ventes vente
                                   LEFT JOIN plats ON vente.code_plat = plats.code_plat
                                   LEFT JOIN clients ON vente.code_client = clients.code_client
                                   WHERE vente.date_vente BETWEEN :date_debut AND :date_fin");
            $stmt->execute([
                ':date_debut' => $date_debut,
                ':date_fin' => $date_fin
            ]);
            // Récupérer les résultats
            $ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = "Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage());
        }
    }

    // Inclure la bibliothèque html2pdf et générer le PDF si tout est bon
    if (empty($error)) {
        require_once '../vendor/autoload.php';
        ob_start(); // Démarre le tampon pour éviter toute sortie avant le PDF

        ?>
        <html>

        <head>
            <title>Rapport des Ventes</title>
            <style>
                body {
                    background-color: #f7fafc;
                    font-family: Arial, sans-serif;
                    line-height: 1.6;
                    color: #2d3748;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                th,
                td {
                    padding: 12px;
                    text-align: left;
                    border: 1px solid #e2e8f0;
                }

                th {
                    background-color: #edf2f7;
                }

                tr:nth-child(odd) {
                    background-color: #ffffff;
                }

                tr:nth-child(even) {
                    background-color: #f7fafc;
                }
            </style>
        </head>

        <body>
            <h1>Rapport des Ventes du <?= htmlspecialchars($date_debut); ?> au <?= htmlspecialchars($date_fin); ?></h1>
            <?php
            // Vérifier s'il y a des ventes
            if (empty($ventes)) {
                echo "<p>Aucune vente trouvée pour la période sélectionnée.</p>";
            } else {

                ?>
                <table>
                    <thead>
                        <tr>
                            <th>CODE</th>
                            <th>Client</th>
                            <th>Plat</th>
                            <th>Nombre de Plats</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ventes as $vente): ?>
                            <tr>
                                <td><?= htmlspecialchars($vente['code_vente']); ?></td>
                                <td><?= htmlspecialchars($vente['nom_client']); ?></td>
                                <td><?= htmlspecialchars($vente['nom_plat']); ?></td>
                                <td><?= htmlspecialchars($vente['nbre_plat']); ?></td>
                                <td><?= htmlspecialchars($vente['date_vente']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </body>

        </html>
        <?php
        $html = ob_get_clean(); // Récupère le contenu tamponné

        // Initialiser html2pdf
        try {
            $pdf = new \Spipu\Html2Pdf\Html2Pdf('P', 'A4', 'fr');
            $pdf->writeHTML($html);
            $pdf->output("rapport_ventes_{$date_debut}_{$date_fin}.pdf");
        } catch (Html2PdfException $e) {
            echo $e;
        }
    }
}
?>