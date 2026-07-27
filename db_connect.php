<?php
// db_connect.php - Connexion à la base de données
$host = 'localhost';
$dbname = 'contact_hamin_site';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   // echo "Connexion réussie !"; // Tu peux enlever le // pour tester
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>