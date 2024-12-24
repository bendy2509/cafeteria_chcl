<?php
session_start();
require_once '../../includes/config.php';

try {
    // Récupération des clients
    $query = $pdo->query("SELECT code_client, nom_client, type_client, phone_client FROM clients");
    $clients = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des clients - Cafétéria</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body class="bg-gray-50 min-h-screen flex">

    <!-- Barre latérale gauche -->
    <aside class="w-64 bg-blue-800 text-white flex flex-col min-h-screen shadow-lg rounded-lg">
        <div class="p-6 text-center font-extrabold text-xl border-b border-blue-700">
            CAFETERIA
        </div>
        <nav class="flex-grow">
            <ul class="space-y-2 mt-4">
                <li>
                    <a href="../../index.php"
                        class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                        <ion-icon name="home-outline" class="mr-2 pr-4"></ion-icon> Dashboard
                    </a>
                </li>
                <li>
                    <a href="./clients.php"
                        class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                        <ion-icon name="people-outline" class="mr-2 pr-4"></ion-icon> Clients
                    </a>
                </li>
                <li>
                    <a href="../plats/plats.php"
                        class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                        <ion-icon name="restaurant-outline" class="mr-2 pr-4"></ion-icon> Plats
                    </a>
                </li>

                <li>
                    <a href="#"
                        class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                        <ion-icon name="cart-outline" class="mr-2 pr-4"></ion-icon> Ventes
                    </a>
                </li>
                <li>
                    <a href="../users/users.php"
                        class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                        <ion-icon name="people-outline" class="mr-2 pr-4"></ion-icon> Users
                    </a>
                </li>
            </ul>
        </nav>
        <div class="p-4 text-center text-sm border-t border-blue-700">
            &copy; 2024 Cafeteria CHCL
        </div>
    </aside>

    <!-- Zone principale -->
    <main class="flex-grow p-6 relative">
        <!-- Entête -->
        <header class="bg-white shadow p-6 rounded-lg text-center mb-6">
            <h1 class="text-3xl font-extrabold text-blue-800">Gestion des Clients</h1>
            <p class="text-gray-500 text-sm mt-2">Ajoutez, modifiez ou supprimez des clients</p>
        </header>

        <!-- Tableau des clients -->
        <section class="bg-white p-6 rounded-lg shadow">
            <?php
            if (!empty($_SESSION['errors'])) {
                echo '<div class="bg-red-100 text-red-700 p-4 rounded mb-4">';
                foreach ($_SESSION['errors'] as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['errors']);
            }

            if (isset($_GET['success'])) {
                echo '<div class="bg-green-100 text-green-700 p-4 rounded mb-4">' . $_GET['success'] . '</div>';
            }
            ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Liste des clients</h2>
                <!-- Bouton d'ouverture du modal -->
                <a href="#" id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <ion-icon name="add-circle-outline" class="align-middle"></ion-icon> Ajouter un client
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse"
                    summary="Tableau affichant la liste des clients avec leurs coordonnées et actions disponibles">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th scope="col" class="border p-2 text-center">CODE</th>
                            <th scope="col" class="border p-2 text-center">NOM</th>
                            <th scope="col" class="border p-2 text-center">TYPE</th>
                            <th scope="col" class="border p-2 text-center">PHONE</th>
                            <th scope="col" class="border p-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clients)): ?>
                            <tr>
                                <td colspan="5" class="border p-2 text-center text-gray-500">Aucun client trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td class="border p-2"><?= htmlspecialchars($client['code_client']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($client['nom_client']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($client['type_client']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($client['phone_client']); ?></td>
                                    <td class="border p-2 text-center flex justify-center gap-4">
                                        <a href="#"
                                            class="text-blue-500 hover:text-blue-700 px-3 py-1 rounded-md border border-blue-500 hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">Modifier</a>
                                        <a href="./delete_client.php?code_client=<?= htmlspecialchars($client['code_client']); ?>"
                                            class="text-red-500 hover:text-red-700 px-3 py-1 rounded-md border border-red-500 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Modal pour ajouter un client -->
        <section>
            <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                <div class="bg-white rounded-lg w-1/3 p-6">
                    <h3 class="text-2xl font-bold text-blue-800 mb-4">Ajouter un client</h3>

                    <!-- Formulaire d'ajout de client -->
                    <form action="./ajouter_clients.php" method="POST">
                        <div class="mb-2">
                            <label for="nom_client" class="block text-gray-700">Nom</label>
                            <input type="text" id="nom_client" name="nom_client" placeholder="Nom"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="type_client" class="block text-gray-700">Type Client</label>
                            <select id="type_client" name="type_client"
                                class="w-full p-2 border border-gray-300 rounded mt-2">
                                <option value="etudiant">Étudiant</option>
                                <option value="professeur">Professeur</option>
                                <option value="personnel_admin">Personnel Admin</option>
                                <option value="inviter">Inviter</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label for="phone_client" class="block text-gray-700">Téléphone</label>
                            <input type="text" id="phone_client" name="phone_client" placeholder="Téléphone"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" id="closeModal"
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Annuler</button>
                            <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 ml-2 rounded hover:bg-blue-600">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

    </main>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>

</html>