<?php
session_start();
require_once '../../includes/config.php';

try {
    // Récupération des plats
    $query = $pdo->query("SELECT code_plat, nom_plat, cuisson_plat, prix_plat, quantite_plat FROM plats");
    $plats = $query->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Gestion des Plats - Cafétéria</title>
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
            <h1 class="text-3xl font-extrabold text-blue-800">Gestion des Plats</h1>
            <p class="text-gray-500 text-sm mt-2">Ajoutez, modifiez ou supprimez des plats</p>
        </header>

        <!-- Tableau des plats -->
        <section class="bg-white p-6 rounded-lg shadow">
            <?php
            // Pour afficher les erreurs
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
                <h2 class="text-xl font-bold">Liste des plats</h2>
                <!-- Bouton pour ajouter un plat -->
                <?php if ($_SESSION['role_user'] == 'admin'): ?>
                    <a href="#" id="openModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        <ion-icon name="add-circle-outline" class="align-middle"></ion-icon> Ajouter un plat
                    </a>
                <?php endif; ?>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-collapse"
                    summary="Tableau affichant la liste des plats avec leurs descriptions, prix et actions disponibles">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th scope="col" class="border p-2 text-center">CODE</th>
                            <th scope="col" class="border p-2 text-center">NOM</th>
                            <th scope="col" class="border p-2 text-center">CUISSON</th>
                            <th scope="col" class="border p-2 text-center">PRIX</th>
                            <th scope="col" class="border p-2 text-center">QUANTIE</th>
                            <th scope="col" class="border p-2 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($plats)): ?>
                            <tr>
                                <td colspan="5" class="border p-2 text-center text-gray-500">Aucun plat trouvé.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($plats as $plat): ?>
                                <tr>
                                    <td class="border p-2"><?= htmlspecialchars($plat['code_plat']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($plat['nom_plat']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($plat['cuisson_plat']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($plat['prix_plat']); ?> HTG</td>
                                    <td class="border p-2"><?= htmlspecialchars($plat['quantite_plat']); ?></td>
                                    <td class="border p-2 text-center flex justify-center gap-4">
                                        <?php if ($_SESSION['role_user'] == 'admin'): ?>
                                            <a href="#"
                                                class="text-blue-500 hover:text-blue-700 px-3 py-1 rounded-md border border-blue-500 hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                id="openEditPlatModal_<?= htmlspecialchars($plat['code_plat']); ?>"
                                                data-codeid="<?= htmlspecialchars($plat['code_plat']); ?>"
                                                data-nom="<?= htmlspecialchars($plat['nom_plat']); ?>"
                                                data-cuisson="<?= htmlspecialchars($plat['cuisson_plat']); ?>"
                                                data-prix="<?= htmlspecialchars($plat['prix_plat']); ?>"
                                                data-quantite="<?= htmlspecialchars($plat['quantite_plat']); ?>"
                                                title="Modifier le plat <?= htmlspecialchars($plat['nom_plat']); ?>">
                                                Modifier
                                            </a>

                                            <!-- Bouton Supprimer actif pour les autres plats -->
                                            <a href="./delete_plat.php?code_plat=<?= htmlspecialchars($plat['code_plat']); ?>"
                                                class="text-red-500 hover:text-red-700 px-3 py-1 rounded-md border border-red-500 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce plat ?')"
                                                title="Supprimer le plat <?= htmlspecialchars($plat['nom_plat']); ?>">
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

        <!-- Modal pour ajouter un plat -->
        <section>
            <!-- Modal -->
            <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                <div class="bg-white rounded-lg w-1/3 p-6">
                    <h3 class="text-2xl font-bold text-blue-800 mb-4">Ajouter un plat</h3>

                    <!-- Formulaire d'ajout de plat -->
                    <form action="./ajouter_plat.php" method="POST">
                        <div class="mb-2">
                            <label for="nom_plat" class="block text-gray-700">Nom du plat</label>
                            <input type="text" id="nom_plat" name="nom_plat" placeholder="Nom du plat"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>

                        <div class="mb-2">
                            <label for="cuisson_plat" class="block text-gray-700">Cuisson</label>
                            <select id="cuisson_plat" name="cuisson_plat"
                                class="w-full p-2 border border-gray-300 rounded mt-2">
                                <option value="cru">Cru</option>
                                <option value="cuit">Cuit</option>
                                <option value="grille">Grillé</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="prix_plat" class="block text-gray-700">Prix</label>
                            <input type="texte" id="prix_plat" name="prix_plat" placeholder="Prix du plat"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="quantite_plat" class="block text-gray-700">Quantite plat</label>
                            <input type="number" id="quantite_plat" name="quantite_plat" placeholder="Quantité plat"
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
            <div id="editPlatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                <div class="bg-white rounded-lg w-1/3 p-6">
                    <h3 class="text-2xl font-bold text-blue-800 mb-4">Modifier Plat</h3>

                    <!-- Formulaire de modification plat -->
                    <form action="update_plat.php" method="POST">
                        <!-- Champs cachés pour passer le code plat -->
                        <input type="hidden" id="id_plat_edit" name="id_plat">

                        <div class="mb-2">
                            <label for="nom_plat_edit" class="block text-gray-700">Nom</label>
                            <input type="text" id="nom_plat_edit" name="nom_plat_edit" placeholder="Nom"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="cuisson_plat_edit" class="block text-gray-700">Cuisson</label>
                            <select id="cuisson_plat_edit" name="cuisson_plat_edit"
                                class="w-full p-2 border border-gray-300 rounded mt-2">
                                <option value="Cru">Cru</option>
                                <option value="Cuit">Cuit</option>
                                <option value="Grille">Grillé</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="prix_plat_edit" class="block text-gray-700">Prix</label>
                            <input type="texte" id="prix_plat_edit" name="prix_plat_edit" placeholder="Prix du plat"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="quantite_plat_edit" class="block text-gray-700">Quantite plat</label>
                            <input type="number" id="quantite_plat_edit" name="quantite_plat_edit"
                                placeholder="Quantité plat" class="w-full p-2 border border-gray-300 rounded mt-2"
                                required>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" id="closeEditPlatModal"
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
            const modalEdit = document.getElementById("editPlatModal");
            const closeModalEdit = document.getElementById("closeEditPlatModal");

            // Ajouter des écouteurs pour tous les boutons "Modifier"
            document.querySelectorAll("[id^='openEditPlatModal_']").forEach(button => {
                button.addEventListener("click", () => {
                    const platId = button.getAttribute("data-codeid");
                    const platNom = button.getAttribute("data-nom");
                    const platCuisson = button.getAttribute("data-cuisson");
                    const platPrix = button.getAttribute("data-prix");
                    const platQuantite = button.getAttribute("data-quantite");

                    //Recuperer le titre du modal
                    const modalTitle = button.getAttribute("data-title");

                    // Pré-remplir les champs du formulaire
                    document.getElementById("nom_plat_edit").value = platNom;
                    document.getElementById("cuisson_plat_edit").value = platCuisson;
                    document.getElementById("prix_plat_edit").value = platPrix;
                    document.getElementById("quantite_plat_edit").value = platQuantite;

                    // Mettre à jour l'ID du plat
                    document.getElementById("id_plat_edit").value = platId;

                    //Mettre a jour
                    document.getElementById("editPlatModal").querySelector("h3").textContent = modalTitle;

                    // Afficher le modal
                    modalEdit.classList.remove("hidden");
                    modalEdit.classList.add("flex");

                    // fermer le modal
                    closeModalEdit.addEventListener("click", () => {
                        modalEdit.classList.add("hidden");
                        modalEdit.classList.remove("flex");
                    });

                    // si plat clic dehors
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