<?php
session_start();
require_once '../../includes/config.php';

try {
    // Récupération des clients
    $query = $pdo->query("SELECT id, code_client, nom_client, type_client, phone_client FROM clients");
    $clients = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur lors de la récupération des données : " . $e->getMessage();
}

// verifier si l'utilisateur est connecté
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
    <title>Gestion des clients - Cafétéria</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>

<body class="bg-gray-50 min-h-screen flex">

    <!-- Barre latérale gauche -->
    <?php
    include '../../includes/sidebar.php';
    ?>

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
            // Le message success dans la session
            if (isset($_SESSION['success'])) {
                echo '<div class="bg-green-100 text-green-800 p-4 rounded mb-4">';
                echo '<p>' . htmlspecialchars($_SESSION['success']) . '</p>';
                echo '</div>';
                unset($_SESSION['success']);
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
                                        <?php if ($_SESSION['role_user'] == 'admin'): ?>
                                            <a href="#"
                                                class="text-blue-500 hover:text-blue-700 px-3 py-1 rounded-md border border-blue-500 hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                id="openEditClientModal_<?= htmlspecialchars($client['id']); ?>"
                                                data-id="<?= htmlspecialchars($client['id']); ?>"
                                                data-nom="<?= htmlspecialchars($client['nom_client']); ?>"
                                                data-type="<?= htmlspecialchars($client['type_client']); ?>"
                                                data-phone="<?= htmlspecialchars($client['phone_client']); ?>"
                                                title="Modifier le client <?= htmlspecialchars($client['nom_client']); ?>">Modifier</a>

                                            <a href="./delete_client.php?code_client=<?= htmlspecialchars($client['code_client']); ?>"
                                                class="text-red-500 hover:text-red-700 px-3 py-1 rounded-md border border-red-500 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')"
                                                title="Supprimer le client <?= htmlspecialchars($client['nom_client']); ?>">Supprimer</a>

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

        <section>
            <!-- Modal de modification -->
            <div id="editClientModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                <div class="bg-white rounded-lg w-1/3 p-6">
                    <h3 class="text-2xl font-bold text-blue-800 mb-4">Modifier Client</h3>

                    <!-- Formulaire de modification client -->
                    <form action="update_client.php" method="POST">
                        <!-- Champs cachés pour passer le code client -->
                        <input type="hidden" id="id_client_edit" name="id_client">

                        <div class="mb-2">
                            <label for="nom_client_edit" class="block text-gray-700">Nom</label>
                            <input type="text" id="nom_client_edit" name="nom_client_edit" placeholder="Nom"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="type_client_edit" class="block text-gray-700">Type</label>
                            <select id="type_client_edit" name="type_client_edit"
                                class="w-full p-2 border border-gray-300 rounded mt-2">
                                <option value="etudiant">Étudiant</option>
                                <option value="professeur">Professeur</option>
                                <option value="personnel_admin">Personnel Admin</option>
                                <option value="inviter">Inviter</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="phone_client_edit" class="block text-gray-700">Téléphone</label>
                            <input type="text" id="phone_client_edit" name="phone_client_edit" placeholder="Téléphone"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" id="closeEditClientModal"
                                class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Annuler</button>
                            <button type="submit"
                                class="bg-blue-500 text-white px-4 py-2 ml-2 rounded hover:bg-blue-600">Modifier</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="../../assets/js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modalEdit = document.getElementById("editClientModal");
            const closeModalEdit = document.getElementById("closeEditClientModal");

            // Ajouter des écouteurs pour tous les boutons "Modifier"
            document.querySelectorAll("[id^='openEditClientModal_']").forEach(button => {
                button.addEventListener("click", () => {
                    const clientId = button.getAttribute("data-id");
                    const clientNom = button.getAttribute("data-nom");
                    const clientType = button.getAttribute("data-type");
                    const clientPhone = button.getAttribute("data-phone");

                    //Recuperer le titre du modal
                    const modalTitle = button.getAttribute("data-title");

                    // Pré-remplir les champs du formulaire
                    document.getElementById("nom_client_edit").value = clientNom || "";
                    document.getElementById("phone_client_edit").value = clientPhone || "";
                    document.getElementById("type_client_edit").value = clientType || "";

                    // Mettre à jour l'ID de l'utilisateur
                    document.getElementById("id_client_edit").value = clientId;

                    //Mettre a jour
                    document.getElementById("editClientModal").querySelector("h3").textContent = modalTitle;

                    // Afficher le modal
                    modalEdit.classList.remove("hidden");
                    modalEdit.classList.add("flex");

                    // fermer le modal
                    closeModalEdit.addEventListener("click", () => {
                        modalEdit.classList.add("hidden");
                        modalEdit.classList.remove("flex");
                    });

                    // si client clic dehors
                    window.addEventListener("click", (e) => {
                        if (e.target === modalEdit) {
                            modalEdit.classList.add("hidden");
                            modalEdit.classList.remove("flex");
                        }
                    });
                });
            });

            // Fermeture du modal
            if (closeModalEdit) {
                closeModalEdit.addEventListener("click", () => {
                    modalEdit.classList.add("hidden");
                    modalEdit.classList.remove("flex");
                });
            }
        });
    </script>
</body>

</html>