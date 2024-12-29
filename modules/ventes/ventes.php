<?php
session_start();
require_once '../../includes/config.php';

try {
    // Récupération des ventes
    $stmt = $pdo->prepare("
        SELECT vente.id, vente.code_client, vente.code_plat, vente.nbre_plat, plats.nom_plat, clients.nom_client
        FROM ventes vente
        LEFT JOIN plats ON vente.code_plat = plats.code_plat
        LEFT JOIN clients ON vente.code_client = clients.code_client
    ");
    $stmt->execute();
    $ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des données : " . $e->getMessage();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Ventes - Cafétéria</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body class="bg-gray-50 min-h-screen flex">
    <!-- Barre latérale gauche -->
    <?php include '../../includes/sidebar.php'; ?>

    <!-- Zone principale -->
    <main class="flex-grow p-6 relative">
        <!-- Entête -->
        <header class="bg-white shadow p-6 rounded-lg text-center mb-6">
            <h1 class="text-3xl font-extrabold text-blue-800">Gestion des Ventes</h1>
            <p class="text-gray-500 text-sm mt-2">Ajoutez, modifiez ou supprimez des ventes</p>
        </header>

        <!-- Tableau des ventes -->
        <section class="bg-white p-6 rounded-lg shadow">
            <!-- Affichage des messages d'erreur et de succès -->
            <?php
            if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])) {
                echo '<div class="bg-red-100 text-red-700 p-4 rounded mb-4">';
                foreach ($_SESSION['errors'] as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['errors']);
            }

            if (isset($_SESSION['success'])) {
                echo '<div class="bg-green-100 text-green-700 p-4 rounded mb-4">';
                echo '<p>' . htmlspecialchars($_SESSION['success']) . '</p>';
                echo '</div>';
                unset($_SESSION['success']);
            }
            ?>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold">Liste des ventes</h2>
                <a href="#" id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <ion-icon name="add-circle-outline" class="align-middle"></ion-icon> Ajouter une vente
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-[#fcb126] text-left">
                            <th class="border p-2 text-center">ID</th>
                            <th class="border p-2 text-center">Client</th>
                            <th class="border p-2 text-center">Plat</th>
                            <th class="border p-2 text-center">Nombre</th>
                            <th class="border p-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ventes)): ?>
                            <tr>
                                <td colspan="5" class="border p-2 text-center text-gray-500">Aucune vente trouvée.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($ventes as $vente): ?>
                                <tr >
                                    <td class="bg-[#ECBF2D] border p-2 text-center"><?= htmlspecialchars($vente['id']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($vente['nom_client']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($vente['nom_plat']); ?></td>
                                    <td class="border p-2 text-center"><?= htmlspecialchars($vente['nbre_plat']); ?></td>
                                    <td class="border p-2 text-center flex justify-center gap-4">
                                        <?php if ($_SESSION['role_user'] == 'admin'): ?>
                                            <a href="#" id="openEditVenteModal_<?= htmlspecialchars($vente['id']); ?>"
                                                data-quantite="<?= htmlspecialchars($vente['nbre_plat']); ?>"
                                                data-id="<?= htmlspecialchars($vente['id']); ?>"
                                                class="text-blue-500 hover:text-blue-700 px-3 py-1 rounded-md border"
                                                title="Modifier la vente n°<?= htmlspecialchars($vente['id']); ?>">
                                                Modifier
                                            </a>

                                            <!-- Bouton Supprimer actif pour les autres ventes -->
                                            <a href="./delete_vente.php?id=<?= htmlspecialchars($vente['id']); ?>"
                                                class="text-red-500 hover:text-red-700 px-3 py-1 rounded-md border border-red-500 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?')"
                                                title="Supprimer la vente n°<?= htmlspecialchars($vente['id']); ?>">
                                                Supprimer
                                            </a>
                                        <?php else: ?>
                                            <!-- Si l'utilisateur n'est pas admin, les boutons ne sont pas affichés -->
                                            <span class="text-gray-400">Aucune action autorisée</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Modal d'ajout de vente -->
        <section>
            <div id="modal" class="fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 hidden">
                <div class="w-full max-w-md p-6 m-auto bg-white rounded-lg shadow-lg relative">
                    <h2 class="text-2xl font-bold mb-4">Ajouter une vente</h2>
                    <form action="./add_vente.php" method="POST" class="space-y-4">
                        <div>
                            <label for="client" class="block text-sm font-medium text-gray-700">Client</label>
                            <select name="client" id="client"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Sélectionnez un client</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM clients");
                                $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($clients as $client) {
                                    echo '<option value="' . $client['code_client'] . '">' . htmlspecialchars($client['nom_client']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="plat" class="block text-sm font-medium text-gray-700">Plat</label>
                            <select name="plat" id="plat"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Sélectionnez un plat</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM plats");
                                $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($plats as $plat) {
                                    echo '<option value="' . $plat['code_plat'] . '">' . htmlspecialchars($plat['nom_plat']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="nbre_plat" class="block text-sm font-medium text-gray-700">Nombre de
                                plats</label>
                            <input type="number" id="nbre_plat" name="nbre_plat" min="1"
                                placeholder="Entrez le nombre de plats"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                Ajouter
                            </button>
                        </div>
                    </form>
                    <button id="closeModal" class="absolute top-0 right-0 mt-4 mr-4">
                        <ion-icon name="close-circle-outline" class="text-3xl text-gray-700"></ion-icon>
                    </button>
                </div>
            </div>
        </section>

        <!-- Modal de modification de vente -->
        <section>
            <div id="modalEdit" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                <div class="w-full max-w-md p-6 m-auto bg-white rounded-lg shadow-lg relative">
                    <h2 class="text-2xl font-bold mb-4">Modifier une vente</h2>
                    <form action="./update_vente.php" method="POST" class="space-y-4">
                        <input type="hidden" name="id" id="id">

                        <div>
                            <label for="client" class="block text-sm font-medium text-gray-700">Client</label>
                            <select name="client" id="client"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Sélectionnez un client</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM clients");
                                $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($clients as $client) {
                                    echo '<option value="' . $client['code_client'] . '">' . htmlspecialchars($client['nom_client']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="plat" class="block text-sm font-medium text-gray-700">Plat</label>
                            <select name="plat" id="plat"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                <option value="">Sélectionnez un plat</option>
                                <?php
                                $stmt = $pdo->query("SELECT * FROM plats");
                                $plats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($plats as $plat) {
                                    echo '<option value="' . $plat['code_plat'] . '">' . htmlspecialchars($plat['nom_plat']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label for="nbre_plat" class="block text-sm font-medium text-gray-700">Nombre de
                                plats</label>
                            <input type="number" id="nbre_plat" name="nbre_plat" min="1"
                                placeholder="Entrez le nombre de plats"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                Modifier
                            </button>
                        </div>
                    </form>
                    <button id="closeEditModal" class="absolute top-0 right-0 mt-4 mr-4">
                        <ion-icon name="close-circle-outline" class="text-3xl text-gray-700"></ion-icon>
                    </button>
                </div>
            </div>
        </section>

    </main>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../../assets/js/script.js"></script>
    <script>
        // Ouvrir le modal de la modification de vente
        const openEditModals = document.querySelectorAll('[id^="openEditVenteModal_"]');
        openEditModals.forEach(openEditModal => {
            openEditModal.addEventListener('click', function () {
                const modalEdit = document.getElementById('modalEdit');
                const id = this.getAttribute('data-id');
                const quantite = this.getAttribute('data-quantite');

                modalEdit.querySelector('#nbre_plat').value = quantite;
                modalEdit.querySelector('#id').value = id;
                modalEdit.classList.remove('hidden');
            });
        });

        // Fermer le modal de modification de vente
        const closeEditModal = document.getElementById('closeEditModal');
        closeEditModal.addEventListener('click', function () {
            const modalEdit = document.getElementById('modalEdit');
            modalEdit.classList.add('hidden');
        });

        // si il clic en dehors du modal, le modal se ferme
        window.addEventListener('click', function (event) {
            const modalEdit = document.getElementById('modalEdit');
            if (event.target === modalEdit) {
                modalEdit.classList.add('hidden');
            }
        });

    </script>
</body>

</html>