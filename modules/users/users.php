<?php
session_start();
require_once '../../includes/config.php';

try {
    // Recuperation des utilisateurs
    $query = $pdo->query("SELECT id, nom_user, pseudo_user, role_user FROM users");
    $users = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des données : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - Cafétéria</title>
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
                    <a href="../clients/clients.php"
                        class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                        <ion-icon name="restaurant-outline" class="mr-2 pr-4"></ion-icon> Clients
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
                    <a href="users.php"
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
            <h1 class="text-3xl font-extrabold text-blue-800">Gestion des Utilisateurs</h1>
            <p class="text-gray-500 text-sm mt-2">Ajoutez, modifiez ou supprimez des utilisateurs</p>
        </header>

        <!-- Tableau des utilisateurs -->
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
                echo '<div class="bg-green-100 text-green-800 p-4 rounded mb-4">';
                echo '<p>' . htmlspecialchars($_GET['success']) . '</p>';
                echo '</div>';
            } elseif (isset($_GET['error'])) {
                echo '<div class="bg-red-100 text-red-800 p-4 rounded mb-4">';
                echo '<p>' . htmlspecialchars($_GET['error']) . '</p>';
                echo '</div>';
            }
            ?>

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Liste des utilisateurs</h2>
                <!-- Bouton d'ouverture du modal -->
                <a href="#" id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <ion-icon name="add-circle-outline" class="align-middle"></ion-icon> Ajouter un utilisateur
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse"
                    summary="Tableau affichant la liste des utilisateurs avec leurs rôles et actions disponibles">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th scope="col" class="border p-2 text-center">Pseudo</th>
                            <th scope="col" class="border p-2 text-center">NOM</th>
                            <th scope="col" class="border p-2 text-center">Rôle</th>
                            <th scope="col" class="border p-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="4" class="border p-2 text-center text-gray-500">Aucun utilisateur trouvé.</td>
                            </tr>
                        <?php else: ?> 
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="border p-2"><?= htmlspecialchars($user['pseudo_user']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($user['nom_user']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($user['role_user']); ?></td>
                                    <td class="border p-2 text-center flex justify-center gap-4">
                                        <a href="#"
                                            class="text-blue-500 hover:text-blue-700 px-3 py-1 rounded-md border border-blue-500 hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">Modifier</a>
                                        <a href="./delete_user.php?id=<?= htmlspecialchars($user['id']); ?>"
                                            class="text-red-500 hover:text-red-700 px-3 py-1 rounded-md border border-red-500 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Modal pour ajouter un utilisateur -->
        <section>
            <!-- Modal -->
            <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                <div class="bg-white rounded-lg w-1/3 p-6">
                    <h3 class="text-2xl font-bold text-blue-800 mb-4">Ajouter un utilisateur</h3>

                    <!-- Formulaire d'ajout d'utilisateur -->
                    <form action="add_user.php" method="POST">
                        <div class="mb-2">
                            <label for="nom_user" class="block text-gray-700">Nom</label>
                            <input type="text" id="nom_user" name="nom_user" placeholder="Nom"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="prenom_user" class="block text-gray-700">Prenom</label>
                            <input type="text" id="prenom_user" name="prenom_user" placeholder="Prenom"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>

                        <div class="mb-2">
                            <label for="pseudo_user" class="block text-gray-700">Pseudo</label>
                            <input type="text" id="pseudo_user" name="pseudo_user" placeholder="Pseudo"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="email_user" class="block text-gray-700">Email</label>
                            <input type="text" id="email_user" name="email_user" placeholder="Email"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>

                        <div class="mb-2">
                            <label for="role_user" class="block text-gray-700">Rôle</label>
                            <select id="role_user" name="role_user"
                                class="w-full p-2 border border-gray-300 rounded mt-2">
                                <option value="admin">Admin</option>
                                <option value="user">Utilisateur</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="password_user" class="block text-gray-700">Password</label>
                            <input type="password" id="password_user" name="password_user" placeholder="Password"
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