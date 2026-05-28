<?php
// infos de co à la BDD MySQL locale
$host     = 'localhost';
$dbname   = 'gestion_casiers';   // nom de la base
$username = 'root';               // user MySQL
$password = 'root1234';           // mdp MySQL

try {
    // création de la connexion PDO avec charset utf8mb4 (supporte les émojis etc.)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // on active les exceptions pour mieux gérer les erreurs BDD
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // si la co échoue on arrête tout et on affiche l'erreur
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>
