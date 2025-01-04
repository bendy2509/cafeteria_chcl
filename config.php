<?php
$host = 'sql113.infinityfree.com';
$dbname = 'if0_38001598_cafeteria_chcl';
$username = 'if0_38001598';
$password = 'Servilus2509'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Gestion de la casse des noms de colonnes
    $pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
