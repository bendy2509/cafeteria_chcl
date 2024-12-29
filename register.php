<?php
session_start();
require_once './includes/config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password || empty($firstname) || empty($name))) {
        $errors[] = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    } elseif ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 4) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE pseudo_user = :username OR email_user = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Le pseudo ou l'email est déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (pseudo_user, email_user, password_user, nom_user, prenom_user) 
                                   VALUES (:username, :email, :password, :firstname, :name)");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashed_password,
                ':name' => $name,
                'firstname' => $firstname
            ]);
            $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
            header("Location: login.php", true, 303);
            exit();
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
    <title>Inscription</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
</head>

<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-indigo-700 to-indigo-900">
    <div class="bg-white shadow-lg rounded-lg w-full max-w-md p-8">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 text-white text-center p-6 rounded-t-lg">
            <h1 class="text-3xl font-bold">Créer un compte</h1>
            <p class="text-sm">Inscrivez-vous pour commencer</p>
        </div>

        <!-- Messages d'erreur -->
        <?php if (!empty($_SESSION['errors'])): ?>
            <div class="bg-red-100 text-red-600 p-4 rounded-lg mt-4">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
                <?php unset($_SESSION['errors']); ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire -->
        <form action="register.php" method="POST" class="mt-6 space-y-4">
            <div class="flex gap-4 w-full">
                <!-- Ajouter un champ pour le prenom -->
                <div>
                    <label for="firstname" class="block text-sm font-medium text-gray-700">Prenom</label>
                    <input type="text" id="firstname" name="firstname" placeholder="Entrez votre pseudo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <!-- Ajouter un champ pour le nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" id="name" name="name" placeholder="Entrez votre pseudo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Pseudo <span
                        class="text-red-600">*</span></label>
                <input type="text" id="username" name="username" placeholder="Entrez votre pseudo"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                        class="text-red-600">*</span></label>
                <input type="email" id="email" name="email" placeholder="Entrez votre email"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe <span
                        class="text-red-600">*</span></label>
                <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirmer le mot de passe
                    <span class="text-red-600">*</span></label>
                <input type="password" id="confirm_password" name="confirm_password"
                    placeholder="Confirmez votre mot de passe"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <button type="submit"
                class="w-full bg-indigo-500 hover:bg-indigo-600 text-white font-semibold py-2 rounded-lg transition duration-200">
                S'inscrire
            </button>
        </form>

        <div class="text-center mt-6">
            <p class="text-sm text-gray-500">Vous avez déjà un compte ? <a href="login.php"
                    class="text-indigo-500 hover:underline">Connectez-vous</a></p>
        </div>
    </div>
</body>

</html>