<?php
/**
 * Démarrer la session pour gérer les sessions utilisateur.
 */
session_start();

/**
 * Inclure le fichier de configuration pour la connexion à la base de données.
 */
require_once './includes/config.php';

/**
 * Initialiser un tableau pour stocker les messages d'erreur.
 */
$errors = [];

/**
 * Vérifier si le formulaire a été soumis.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et nettoyer les données du formulaire.
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $firstname = trim($_POST['firstname']);
    $name = trim($_POST['name']);

    // Vérifier si tous les champs sont remplis.
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || empty($firstname) || empty($name)) {
        $errors[] = "Tous les champs sont obligatoires.";
    }
    // Vérifier si l'adresse email est valide.
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Adresse email invalide.";
    }
    // Vérifier si les mots de passe correspondent.
    elseif ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas.";
    }
    // Vérifier la longueur du mot de passe.
    elseif (strlen($password) < 8) {
        $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
    } else {
        // Préparer une requête pour vérifier si le pseudo ou l'email est déjà utilisé.
        $stmt = $pdo->prepare("SELECT id FROM users WHERE pseudo_user = :username OR email_user = :email");
        $stmt->execute([':username' => $username, ':email' => $email]);

        // Vérifier si le pseudo ou l'email est déjà utilisé.
        if ($stmt->rowCount() > 0) {
            $errors[] = "Le pseudo ou l'email est déjà utilisé.";
        } else {
            // Hacher le mot de passe.
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Préparer une requête pour insérer un nouvel utilisateur dans la base de données.
            $stmt = $pdo->prepare("INSERT INTO users (pseudo_user, email_user, password_user, nom_user, prenom_user, statut) 
                                   VALUES (:username, :email, :password, :firstname, :name, :statut)");
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashed_password,
                ':name' => $name,
                ':firstname' => $firstname,
                ':statut' => 0
            ]);

            // Stocker un message de succès dans la session et rediriger vers la page de connexion.
            $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter après qu'un admin vous y autorise.";
            header("Location: login.php", true, 303);
            exit();
        }
    }

    // Stocker les messages d'erreur dans la session.
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
                    <input type="text" id="firstname" name="firstname" placeholder="Entrez votre prenom"
                        value="<?php echo isset($firstname) ? htmlspecialchars($firstname) : ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
                <!-- Ajouter un champ pour le nom -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nom</label>
                    <input type="text" id="name" name="name" placeholder="Entrez votre nom"
                        value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Pseudo <span
                        class="text-red-600">*</span></label>
                <input type="text" id="username" name="username" placeholder="Entrez votre pseudo"
                    value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email <span
                        class="text-red-600">*</span></label>
                <input type="email" id="email" name="email" placeholder="Entrez votre email"
                    value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"
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