<?php
$base_url = 'http://localhost/cafeteria/';
?>
<aside class="w-72 bg-blue-900 text-white flex flex-col min-h-screen shadow-md rounded-lg overflow-hidden">
    <!-- Logo / Titre -->
    <div class="bg-blue-800 p-6 text-center font-extrabold text-2xl tracking-wide uppercase">
        Cafeteria
    </div>

    <!-- Navigation -->
    <nav class="flex-grow">
        <ul class="mt-6 space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="<?php echo $base_url; ?>index.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-blue-700 hover:text-white rounded transition-all duration-300">
                    <ion-icon name="home-outline" class="mr-3 text-2xl"></ion-icon>
                    Dashboard
                </a>
            </li>
            <!-- Clients -->
            <li>
                <a href="<?php echo $base_url; ?>modules/clients/clients.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-blue-700 hover:text-white rounded transition-all duration-300">
                    <ion-icon name="people-outline" class="mr-3 text-2xl"></ion-icon>
                    Clients
                </a>
            </li>
            <!-- Plats -->
            <li>
                <a href="<?php echo $base_url; ?>modules/plats/plats.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-blue-700 hover:text-white rounded transition-all duration-300">
                    <ion-icon name="restaurant-outline" class="mr-3 text-2xl"></ion-icon>
                    Plats
                </a>
            </li>
            <!-- Ventes -->
            <li>
                <a href="<?php echo $base_url; ?>modules/ventes/ventes.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-blue-700 hover:text-white rounded transition-all duration-300">
                    <ion-icon name="cart-outline" class="mr-3 text-2xl"></ion-icon>
                    Ventes
                </a>
            </li>
            <!-- Users -->
            <li>
                <a href="<?php echo $base_url; ?>modules/users/users.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-blue-700 hover:text-white rounded transition-all duration-300">
                    <ion-icon name="people-circle-outline" class="mr-3 text-2xl"></ion-icon>
                    Users
                </a>
            </li>
            <!-- Déconnexion -->
            <li class="mt-8">
                <a href="<?php echo $base_url; ?>logout.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-red-700 text-white rounded transition-all duration-300">
                    <ion-icon name="log-out-outline" class="mr-3 text-2xl"></ion-icon>
                    Déconnexion
                </a>
            </li>
        </ul>
    </nav>

    <!-- Profil utilisateur -->
    <div class="bg-blue-800 py-4">
        <div class="text-center">
            <ion-icon name="person-circle-outline" class="text-4xl mb-2"></ion-icon>
            <div class="font-semibold text-lg">
                <?php
                if (isset($_SESSION['prenom_user']) && isset($_SESSION['nom_user'])) {
                    echo htmlspecialchars($_SESSION['prenom_user']) . ' ' . htmlspecialchars($_SESSION['nom_user']);
                } else {
                    echo "Utilisateur";
                }
                ?>
            </div>
            <p class="text-sm text-blue-300">Connecté</p>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 bg-blue-800 text-blue-400 text-sm">
        &copy; 2024 Cafeteria CHCL
    </footer>
</aside>