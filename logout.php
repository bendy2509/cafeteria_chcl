<?php
// La page de Déconnexion
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit();