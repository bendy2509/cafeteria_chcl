<?php
// La page de Déconnexion

// Démarrer la session
session_start();

// Vider la session
session_unset();

// Détruire la session
session_destroy();
// Rediriger vers la page de connexion
header("Location: login.php");
exit();