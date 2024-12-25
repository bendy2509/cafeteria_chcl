<?php
session_start();
require_once './includes/config.php';
// Verifier si le formulaire a ete soumis
$error = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Verifier les champs
    if (empty($username) || empty($password)) {
        $error[] = "Tous les champs sont obligatoires.";
    } else {
        // Requete pour recuperer l'utilisateur
        $stmt = $pdo->prepare("SELECT id, pseudo_user, email_user, password_user, nom_user, prenom_user FROM users WHERE pseudo_user = :username OR email_user = :username");
        $stmt->execute([
            ':username' => $username
        ]);
        // recuperer le resultat
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Verifier si l'utilisateur existe
        if ($user) {
            // Vérifier le mot de passe
            if (password_verify($password, $user['password_user'])) {
                // Connexion réussie
                $_SESSION['id'] = $user['id'];
                $_SESSION['pseudo_user'] = $user['pseudo_user'];
                $_SESSION['nom_user'] = $user['nom_user'];
                $_SESSION['prenom_user'] = $user['prenom_user'];

                header("Location: ./index.php", true, 303);
                exit();
            } else {
                // Mot de passe incorrect
                $error[] = "Pseudo/Email ou mot de passe incorrect.";
            }
            $error[] = "Pseudo/Email ou mot de passe incorrect.";
        }
    }
    // Enregistrer les erreurs dans la session
    $_SESSION['errors'] = $error;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'Authentification</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <style>
        .card {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            z-index: 0;
        }

        .card::before {
            content: '';
            position: absolute;
            width: 400px;
            background-image: linear-gradient(180deg, rgb(0, 183, 255), rgb(255, 48, 255));
            height: 400px;
            animation: rotBGimg 4s linear infinite;
            transition: all .2s linear;
        }

        /* Animation de rotation */
        @keyframes rotBGimg {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .card::after {
            content: '';
            position: absolute;
            inset: 5px;
            background: #07182E;
            border-radius: 15px;
            z-index: 1;
        }

        .card img {
            border-radius: 15px;
            position: relative;
            z-index: 2;
        }
    </style>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-xl flex overflow-hidden w-full max-w-4xl">
        <!-- Section gauche avec image -->
        <div class="hidden md:flex items-center justify-center w-1/2">
            <div class="card relative w-full h-full overflow-hidden rounded-lg p-4">
                <img src="./assets/images/resto.jpg" alt="Illustration"
                    class="absolute inset-0 w-full h-full object-cover rounded-lg">
            </div>
        </div>

        <!-- Section droite avec formulaire -->
        <div class="w-full md:w-1/2 p-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center">Bienvenue</h2>
            <p class="text-gray-600 text-center mt-2">Connectez-vous à votre compte</p>
            <?php
            if (!empty($_SESSION['errors'])) {
                echo '<div class="bg-red-100 text-red-700 p-4 rounded mb-4">';
                foreach ($_SESSION['errors'] as $error) {
                    echo '<p>' . htmlspecialchars($error) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['errors']);
            }
            ?>
            <form action="login.php" method="POST" class="space-y-6 mt-8">
                <!-- Champ pseudo ou email -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Pseudo ou Email</label>
                    <input type="text" id="username" name="username" placeholder="Entrez votre pseudo ou email"
                        class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500"
                        required>
                </div>

                <!-- Champ mot de passe -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Mot de Passe</label>
                    <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe"
                        class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-500"
                        required>
                </div>

                <!-- Bouton de connexion -->
                <div>
                    <button type="submit"
                        class="w-full bg-blue-500 text-white font-semibold py-3 rounded-lg hover:bg-blue-600 transition duration-300">
                        Connexion
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>