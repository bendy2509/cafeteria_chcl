<?php
// demarrer la session
session_start();
// inclure le fichier de configuration
require_once 'includes/config.php';

try {
    // Préparation des requêtes SQL
    $queries = [
        'totalPlats' => "SELECT COUNT(*) FROM plats",
        'totalClients' => "SELECT COUNT(*) FROM clients",
        'totalVentes' => "SELECT COUNT(*) FROM ventes",
        'totalUsers' => "SELECT COUNT(*) FROM users",
    ];

    // Exécution et récupération des résultats
    $data = [];
    foreach ($queries as $key => $sql) {
        $stmt = $pdo->query($sql);
        $data[$key] = $stmt->fetchColumn();
    }
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des données : " . htmlspecialchars($e->getMessage());
}

// verifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cafétéria</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>

<body class="bg-gray-50 min-h-screen flex">

    <!-- Barre latérale gauche -->
    <?php
    include 'includes/sidebar.php';
    ?>

    <!-- Zone principale -->
    <main class="flex-grow p-6">
        <!-- En-tête -->
        <header class="bg-white shadow p-6 rounded-lg text-center mb-6">
            <h1 class="text-4xl font-extrabold text-blue-800">CAFETERIA DU CHCL</h1>
            <p class="text-gray-500 text-sm mt-2">Bienvenue sur votre tableau de bord</p>
        </header>

        <!-- Les cartes -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            $cards = [
                ['title' => 'Total des Plats', 'icon' => 'restaurant-outline', 'value' => $data['totalPlats'], 'bgClass' => 'from-blue-500 to-blue-600'],
                ['title' => 'Total des Clients', 'icon' => 'people-outline', 'value' => $data['totalClients'], 'bgClass' => 'from-green-500 to-green-600'],
                ['title' => 'Total des Ventes', 'icon' => 'cart-outline', 'value' => $data['totalVentes'], 'bgClass' => 'from-orange-500 to-orange-600'],
                ['title' => 'Total des Users', 'icon' => 'people-outline', 'value' => $data['totalUsers'], 'bgClass' => 'from-indigo-500 to-indigo-600'],
            ];

            foreach ($cards as $card): ?>
                <div
                    class="bg-gradient-to-br <?= $card['bgClass']; ?> text-white p-6 rounded-lg shadow-md hover:shadow-lg transition">
                    <div class="flex items-center">
                        <ion-icon name="<?= $card['icon']; ?>" class="text-4xl mr-4"></ion-icon>
                        <div>
                            <h3 class="text-lg font-semibold"><?= htmlspecialchars($card['title']); ?></h3>
                            <p class="text-5xl font-extrabold mt-2"><?= htmlspecialchars($card['value']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>