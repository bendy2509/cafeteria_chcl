<?php
$base_url = 'http://localhost/cafeteria/';
?>
<aside class="w-64 bg-blue-800 text-white flex flex-col min-h-screen shadow-lg rounded-lg">
    <div class="p-6 text-center font-extrabold text-xl border-b border-blue-700">
        CAFETERIA
    </div>
    <nav class="flex-grow">
        <ul class="space-y-2 mt-4">
            <li>
                <a href="<?php echo $base_url; ?>index.php"
                    class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                    <ion-icon name="home-outline" class="mr-2 pr-4 text-xl"></ion-icon> Dashboard
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>modules/clients/clients.php"
                    class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                    <ion-icon name="restaurant-outline" class="mr-2 pr-4 text-xl"></ion-icon> Clients
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>modules/plats/plats.php"
                    class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                    <ion-icon name="restaurant-outline" class="mr-2 pr-4 text-xl"></ion-icon> Plats
                </a>
            </li>
            <li>
                <a href="#"
                    class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                    <ion-icon name="cart-outline" class="mr-2 pr-4 text-xl"></ion-icon> Ventes
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>modules/users/users.php"
                    class="flex items-center font-bold p-4 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                    <ion-icon name="people-outline" class="mr-2 pr-4 text-xl"></ion-icon> Users
                </a>
            </li>
            <li>
                <a href="<?php echo $base_url; ?>logout.php"
                    class="flex items-center font-bold p-4 mt-10 hover:bg-white hover:text-blue-950 hover:rounded-lg rounded transition">
                    <ion-icon name="log-out-outline" class="mr-2 pr-4 text-xl"></ion-icon> Déconnexion
                </a>
            </li>
        </ul>
    </nav>
    <div class="pt-4 pl-0 text-sm mb-4">
        <p class="text-center text-2xl font-bold">
            <ion-icon name="person-circle-outline" class="text-xl text-center"></ion-icon>
            <?php
            if (isset($_SESSION['prenom_user']) && isset($_SESSION['nom_user'])) {
                echo htmlspecialchars($_SESSION['prenom_user']) . ' ' . htmlspecialchars($_SESSION['nom_user']);
            }
            ?>
        </p>
    </div>
    <div class="p-4 text-center text-sm border-t border-blue-700">
        &copy; 2024 Cafeteria CHCL
    </div>
</aside>