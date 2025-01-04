<?php
session_start();
require_once '../../includes/config.php';

// Vérification de la connexion
if (!isset($_SESSION['id'])) {
    header("Location: ../../login.php");
    exit;
}

try {
    // Recuperation des utilisateurs
    $query = $pdo->query("SELECT id, prenom_user, nom_user, pseudo_user, role_user, statut FROM users");
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
    <?php
    include '../../includes/sidebar.php';
    ?>

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
            // Le message success dans la session
            if (isset($_SESSION['success'])) {
                echo '<div class="bg-green-100 text-green-800 p-4 rounded mb-4">';
                echo '<p>' . htmlspecialchars($_SESSION['success']) . '</p>';
                echo '</div>';
                unset($_SESSION['success']);
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
                        <tr class="bg-[#fcb126] text-left text-white">
                            <th scope="col" class="border p-2 text-center">Pseudo</th>
                            <th scope="col" class="border p-2 text-center">NOM</th>
                            <th scope="col" class="border p-2 text-center">ROLE</th>
                            <th scope="col" class="border p-2 text-center">STATUT</th>
                            <th scope="col" class="border p-2 text-center">ACTIONS</th>
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
                                    <td class="bg-[#FBEA92] border p-2"><?= htmlspecialchars($user['pseudo_user']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($user['nom_user']); ?></td>
                                    <td class="border p-2"><?= htmlspecialchars($user['role_user']); ?></td>
                                    <td class="bg-[#FBEA92] border p-2 text-center flex justify-center gap-4">
                                        <?php if ($_SESSION['role_user'] == 'admin'): ?>
                                            <?php if ($user['id'] == $_SESSION['id']): ?>
                                                <!-- Activer le bouton Modifier pour l'utilisateur connecte -->
                                                <a href="#"
                                                    class="text-blue-500 hover:text-blue-700 px-3 py-1 rounded-md border border-blue-500 hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                    id="openEditUserModal_<?= htmlspecialchars($user['id']); ?>"
                                                    data-id="<?= htmlspecialchars($user['id']); ?>"
                                                    data-nom="<?= htmlspecialchars($user['nom_user']); ?>"
                                                    data-role="<?= htmlspecialchars($user['role_user']); ?>"
                                                    data-prenom="<?= htmlspecialchars($user['prenom_user']); ?>"
                                                    data-title="Modifier vos informations">Modifier</a>
                                                <!-- Desactiver le bouton Supprimer -->
                                                <button
                                                    class="text-gray-400 bg-gray-200 px-3 py-1 rounded-md border border-gray-300 cursor-not-allowed"
                                                    disabled aria-disabled="true"
                                                    title="Vous ne pouvez pas vous supprimer vous-même">Supprimer</button>
                                            <?php else: ?>
                                                <!-- Boutons actifs pour les autres utilisateurs -->
                                                <a href="#"
                                                    class="text-blue-500 hover:text-blue-700 px-3 py-1 rounded-md border border-blue-500 hover:border-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300"
                                                    id="openEditUserModal_<?= htmlspecialchars($user['id']); ?>"
                                                    data-id="<?= htmlspecialchars($user['id']); ?>"
                                                    data-nom="<?= htmlspecialchars($user['nom_user']); ?>"
                                                    data-role="<?= htmlspecialchars($user['role_user']); ?>"
                                                    data-prenom="<?= htmlspecialchars($user['prenom_user']); ?>"
                                                    data-title="Modifier l'utilisateur <?= htmlspecialchars($user['pseudo_user']); ?>">Modifier</a>
                                                <a href="./delete_user.php?id=<?= htmlspecialchars($user['id']); ?>"
                                                    class="text-red-500 hover:text-red-700 px-3 py-1 rounded-md border border-red-500 hover:border-red-700 focus:outline-none focus:ring-2 focus:ring-red-300"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')"
                                                    title="Supprimer l'utilisateur <?= htmlspecialchars($user['pseudo_user']); ?>">Supprimer</a>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <!-- Si l'utilisateur n'est pas admin, les boutons ne sont pas affiches -->
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

        <!-- Modal pour ajouter un utilisateur -->
        <section>
            <!-- Modal -->
            <div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                <div class="bg-[#fcb126] rounded-lg w-1/3 p-6 pt-0">
                    <h3 class="text-2xl font-bold text-blue-800 mb-2 mt-0 pt-0">Ajouter un utilisateur</h3>

                    <!-- Formulaire d'ajout d'utilisateur -->
                    <form action="add_user.php" method="POST" class="bg-[#FBEA92] p-2 rounded-lg">
                        <div class="flex justify-center items-center gap-6">
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
                        </div>
                        <div class="flex justify-center items-center gap-6">
                            <div class="mb-2">
                                <label for="pseudo_user" class="block text-gray-700">Pseudo</label>
                                <input type="text" id="pseudo_user" name="pseudo_user" placeholder="Pseudo"
                                    class="w-full p-2 border border-gray-300 rounded mt-2" required>
                            </div>
                            <div class="mb-2">
                                <label for="email_user" class="block text-gray-700">Email</label>
                                <input type="email" id="email_user" name="email_user" placeholder="Email"
                                    class="w-full p-2 border border-gray-300 rounded mt-2" required>
                            </div>
                        </div>

                        <div class="mb-1">
                            <label for="role_user" class="block text-gray-700">Rôle</label>
                            <select id="role_user" name="role_user"
                                class="w-full p-2 border border-gray-300 rounded mt-2">
                                <option value="admin">Admin</option>
                                <option value="user">Utilisateur</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="password_user" class="block text-gray-700">Mot de passe</label>
                            <input type="password" id="password_user" name="password_user" placeholder="Password"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="password_user1" class="block text-gray-700">Confirmer le mot de passe</label>
                            <input type="password" id="password_user1" name="password_user1" placeholder="Password"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>

                        <div class="flex justify-end mt-4">
                            <button type="button" id="closeModal"
                                class="bg-red-500 text-white px-4 py-1 rounded hover:bg-red-600">Annuler</button>
                            <button type="submit"
                                class="bg-blue-500 text-white px-4 py-1 ml-2 rounded hover:bg-blue-600">Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Modal pour modifier un utilisateur -->
        <section>
            <!-- Modal de modification -->
            <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
                <div class="bg-[#fcb126] rounded-lg w-1/3 p-6">
                    <h3 class="text-2xl font-bold text-blue-800 mb-4">Modifier Utilisateur</h3>

                    <!-- Formulaire de modification d'utilisateur -->
                    <form action="update_user.php" method="POST" class="bg-[#FBEA92] p-2 rounded-lg">
                        <!-- Champs cachés pour passer l'ID de l'utilisateur -->
                        <input type="hidden" id="id_user_edit" name="id_user">

                        <div class="mb-2">
                            <label for="nom_user_edit" class="block text-gray-700">Nom</label>
                            <input type="text" id="nom_user_edit" name="nom_user_edit" placeholder="Nom"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="mb-2">
                            <label for="prenom_user_edit" class="block text-gray-700">Prenom</label>
                            <input type="text" id="prenom_user_edit" name="prenom_user_edit" placeholder="Prenom"
                                class="w-full p-2 border border-gray-300 rounded mt-2" required>
                        </div>
                        <div class="flex justify-between items-center mb-2 gap-4">
                            <div class="mb-2">
                                <label for="role_user_edit" class="block text-gray-700">Rôle</label>
                                <select id="role_user_edit" name="role_user_edit"
                                    class="w-full p-2 border border-gray-300 rounded mt-2">
                                    <option value="admin">Admin</option>
                                    <option value="user">Utilisateur</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="statut_user_edit" class="block text-gray-700">Statut</label>
                                <select id="statut_user_edit" name="statut_user_edit"
                                    class="w-full p-2 border border-gray-300 rounded mt-2">
                                    <option value="true">Activé (e)</option>
                                    <option value="false">Désactivé (e)</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="password_user" class="block text-gray-700">Mot de passe</label>
                            <input type="password" id="password_user_edit" name="password_user_edit"
                                placeholder="Saisir le nouveau mot de passe..."
                                class="w-full p-2 border border-gray-300 rounded mt-2">
                        </div>

                        <div class="flex justify-end">
                            <button type="button" id="closeEditUserModal"
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
            const modalEdit = document.getElementById("editUserModal");
            const closeModalEdit = document.getElementById("closeEditUserModal");

            // Ajouter des écouteurs pour tous les boutons "Modifier"
            document.querySelectorAll("[id^='openEditUserModal_']").forEach(button => {
                button.addEventListener("click", () => {
                    const userId = button.getAttribute("data-id");
                    const userPseudo = button.getAttribute("data-pseudo");
                    const userNom = button.getAttribute("data-nom");
                    const userRole = button.getAttribute("data-role");
                    const userPrenom = button.getAttribute("data-prenom");

                    //L'atribut data-title est utilisé pour passer le titre du modal
                    const modalTitle = button.getAttribute("data-title");

                    // Pré-remplir les champs du formulaire
                    document.getElementById("nom_user_edit").value = userNom || "";
                    document.getElementById("prenom_user_edit").value = userPrenom || "";
                    document.getElementById("role_user_edit").value = userRole || "";

                    // Mettre à jour l'ID de l'utilisateur
                    document.getElementById("id_user_edit").value = userId;

                    // Mettre à jour le titre du modal
                    modalEdit.querySelector("h3").textContent = modalTitle;

                    // Afficher le modal
                    modalEdit.classList.remove("hidden");
                    modalEdit.classList.add("flex");

                    // fermer le modal
                    closeModalEdit.addEventListener("click", () => {
                        modalEdit.classList.add("hidden");
                        modalEdit.classList.remove("flex");
                    });

                    // si user clic dehors
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