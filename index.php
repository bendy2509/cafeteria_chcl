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

//Verifier que c'est un admin
if ($_SESSION['role_user'] !== 'admin') {
    header("Location: ./modules/ventes/ventes.php");
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
    <main class="flex-grow p-6 min-h-screen relative">
        <!-- En-tête -->
        <header class="shadow p-6 rounded-lg text-center mb-6">
            <h1 class="text-4xl font-extrabold text-blue-900">CAFETERIA DU CHCL</h1>
            <p class="text-gray-500 text-sm mt-2">Bienvenue sur votre tableau de bord</p>
        </header>

        <!-- Les cartes -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            $cards = [
                [
                    'title' => 'Total des Plats',
                    'icon' => 'restaurant-outline',
                    'value' => $data['totalPlats'],
                    'bgClass' => 'from-orange-500 to-orange-600',
                    'link' => './modules/plats/plats.php'
                ],
                [
                    'title' => 'Total des Clients',
                    'icon' => 'people-outline',
                    'value' => $data['totalClients'],
                    'bgClass' => 'from-green-500 to-green-600',
                    'link' => './modules/clients/clients.php'
                ],
                [
                    'title' => 'Total des Ventes',
                    'icon' => 'cart-outline',
                    'value' => $data['totalVentes'],
                    'bgClass' => 'from-orange-500 to-orange-600',
                    'link' => './modules/ventes/ventes.php'
                ],
                [
                    'title' => 'Total des Users',
                    'icon' => 'people-outline',
                    'value' => $data['totalUsers'],
                    'bgClass' => 'from-[#15616D] to-[#15616D]',
                    'link' => './modules/users/users.php'
                ],
            ];

            foreach ($cards as $card): ?>
                <a href="<?= $card['link']; ?>">
                    <div
                        class="bg-gradient-to-br <?= $card['bgClass']; ?> 
                       text-white p-6 rounded-lg shadow-lg hover:shadow-xl transition duration-300 transform hover:scale-105">
                        <div class="flex items-center">
                            <ion-icon name="<?= $card['icon']; ?>" class="text-4xl mr-4"></ion-icon>
                            <div>
                                <h3 class="text-lg font-semibold"><?= htmlspecialchars($card['title']); ?></h3>
                                <p class="text-5xl font-extrabold mt-2"><?= htmlspecialchars($card['value']); ?></p>
                            </div>
                        </div>
                    </div>
                </a>

            <?php endforeach; ?>
        </div>
        <!-- Ajout du bouton pour générer un rapport centré vers le bas -->
        <section class="absolute bottom-0 left-0 mx-auto w-full flex justify-center items-center mb-6">
            <button id="openModal"
                class="p-6 bg-gradient-to-r from-green-500 to-green-700 hover:from-green-600 hover:to-green-800 text-white font-bold text-lg rounded-full shadow-lg transition transform hover:scale-105 focus:ring-4 focus:ring-green-300 focus:outline-none">
                <ion-icon name="document-text-outline" class="text-2xl mr-2 align-middle"></ion-icon>
                Générer un rapport
            </button>
        </section>

        <section>
            <!-- Modal  -->
            <div id="modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 items-center justify-center hidden">
                <div class="bg-white rounded-lg shadow-lg p-6 w-[400px]">
                    <h2 class="text-xl font-bold mb-4 text-gray-800">Sélectionner les dates</h2>
                    <form id="dateForm" class="space-y-4" method="POST" action="generateRapport/rapport.php">
                        <!-- Champ Date Début -->
                        <div>
                            <label for="date_debut" class="block text-sm font-medium text-gray-700">Date Début</label>
                            <input type="date" id="date_debut" name="date_debut"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <!-- Champ Date de Fin -->
                        <div>
                            <label for="date_fin" class="block text-sm font-medium text-gray-700">Date de Fin</label>
                            <input type="date" id="date_fin" name="date_fin"
                                class="w-full px-3 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <!-- Boutons -->
                        <div class="flex justify-end space-x-4">
                            <button type="button" id="closeModal"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Annuler
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                                Confirmer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>


    </main>




    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script>
        // Récupération des éléments du DOM
        const openModal = document.getElementById('openModal');
        const closeModal = document.getElementById('closeModal');
        const modal = document.getElementById('modal');

        // Afficher le modal
        openModal.addEventListener('click', () => {
            modal.classList.remove('hidden');
            // Ajouter la classe 'flex' pour centrer le modal
            modal.classList.add('flex');
        });

        // Masquer le modal
        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden');
            // Retirer la classe 'flex' pour centrer le modal
            modal.classList.remove('flex');
        });
    </script>
</body>

</html>