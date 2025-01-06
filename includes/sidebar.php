<?php
$base_url = 'http://localhost/cafeteria_chcl/';
?>
<aside class="w-72 bg-[#CA6207] text-white flex flex-col min-h-screen shadow-md rounded-lg overflow-hidden">
    <!-- Logo / Titre -->
    <div class="bg-[#fcb126] p-6 text-center font-extrabold text-2xl tracking-wide uppercase">
        CAFETERIA CHCL
    </div>

    <!-- Navigation -->
    <nav class="flex-grow">
        <ul class="mt-6 space-y-2">
            <!-- Dashboard -->
            <?php if (isset($_SESSION['role_user']) && $_SESSION['role_user'] === 'admin') : ?>
                        <li>
                            <a href="<?php echo $base_url; ?>index.php"
                                class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-[#fcb126] hover:text-white rounded transition-all duration-300">
                                <ion-icon name="home-outline" class="mr-3 text-2xl"></ion-icon>
                                Tableau de bord
                            </a>
                        </li>
            <?php endif; ?>
            <!-- Clients -->
            <li>
                <a href="<?php echo $base_url; ?>modules/clients/clients.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-[#fcb126] hover:text-white rounded transition-all duration-300">
                    <ion-icon name="people-outline" class="mr-3 text-2xl"></ion-icon>
                    Clients
                </a>
            </li>
            <!-- Plats -->
            <li>
                <a href="<?php echo $base_url; ?>modules/plats/plats.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-[#fcb126] hover:text-white rounded transition-all duration-300">
                    <ion-icon name="restaurant-outline" class="mr-3 text-2xl"></ion-icon>
                    Plats
                </a>
            </li>
            <!-- Ventes -->
            <li>
                <a href="<?php echo $base_url; ?>modules/ventes/ventes.php"
                    class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-[#fcb126] hover:text-white rounded transition-all duration-300">
                    <ion-icon name="cart-outline" class="mr-3 text-2xl"></ion-icon>
                    Ventes
                </a>
            </li>
            <!-- Users -->
            <?php if (isset($_SESSION['role_user']) && $_SESSION['role_user'] === 'admin') : ?>
                        <li>
                            <a href="<?php echo $base_url; ?>modules/users/users.php"
                                class="flex items-center px-5 py-3 font-semibold text-lg hover:bg-[#fcb126] hover:text-white rounded transition-all duration-300">
                                <ion-icon name="people-circle-outline" class="mr-3 text-2xl"></ion-icon>
                                Utilisateur
                            </a>
                        </li>
            <?php endif; ?>
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
    <div class="bg-[#15616D] py-4 pb-0">
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
    <footer class="text-center py-2 bg-[#15616D] text-blue-400 text-sm">
        &copy; 2024 Cafeteria CHCL
    </footer>
</aside>