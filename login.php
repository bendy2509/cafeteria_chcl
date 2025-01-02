<?php
session_start();
require_once './includes/config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $errors[] = "Tous les champs sont obligatoires.";
    } else {
        $stmt = $pdo->prepare("SELECT id, pseudo_user, email_user, password_user, nom_user, prenom_user, 
                               role_user, statut FROM users 
                               WHERE pseudo_user = :username OR email_user = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_user'])) {
            //Verifier si le statut du user est 1
            if ($user['statut'] == 0) {
                $errors[] = "Votre compte a été désactivé. Veuillez contacter l'administrateur.";
            } else {
                session_regenerate_id(true);
                $_SESSION['id'] = $user['id'];
                $_SESSION['pseudo_user'] = $user['pseudo_user'];
                $_SESSION['nom_user'] = $user['nom_user'];
                $_SESSION['prenom_user'] = $user['prenom_user'];
                $_SESSION['role_user'] = $user['role_user'];

                if ($user['role_user'] === 'admin') {
                    header("Location: ./index.php", true, 303);
                } else {
                    header("Location: ./modules/ventes/ventes.php", true, 303);
                }
                exit();
            }
        } else {
            $errors[] = "Pseudo/Email ou mot de passe incorrect.";
        }
    }

    $_SESSION['errors'] = $errors;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-[#15616D] to-indigo-900">
    <div class="bg-[#fcb126] shadow-lg rounded-lg w-full max-w-md p-8">
        <div class="bg-[#15616D] to-blue-700 text-white text-center p-6 rounded-t-lg">
            <h1 class="text-3xl font-bold">Bienvenue</h1>
            <p class="text-sm">Connectez-vous pour accéder à votre compte</p>
        </div>

        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="bg-red-100 text-red-600 p-4 rounded-lg mt-4">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif;
        // Le message success dans la session
        if (isset($_SESSION['success'])) {
            echo '<div class="bg-green-100 text-green-800 p-4 rounded mb-4">';
            echo '<p>' . htmlspecialchars($_SESSION['success']) . '</p>';
            echo '</div>';
            unset($_SESSION['success']);
        }
        ?>

        <form action="login.php" method="POST" class="mt-6 space-y-4 bg-[#FBEA92] p-4 rounded-lg">
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Pseudo ou Email</label>
                <input type="text" id="username" name="username" placeholder="Entrez votre pseudo ou email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>
            <button type="submit"
                class="w-full bg-[#15616D] hover:bg-blue-600 text-white font-semibold py-2 rounded-lg transition duration-200">
                Connexion
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">Vous n'avez pas de compte ? <a href="register.php"
                    class="text-blue-500 hover:underline">Inscrivez-vous</a></p>
        </div>
    </div>
</body>

</html>