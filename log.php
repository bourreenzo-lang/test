<?php
session_start();
require_once 'connbdd.php';

try {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE nom = :name');
    $stmt->bindParam(':name', $_SESSION['user_name']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_id = $user['id'];
        $user_name = $user['nom'];
        $timezone = new DateTimeZone('Europe/Paris');
        $dateTime = new DateTime('now', $timezone);
        $timestamp = $dateTime->format('Y-m-d H:i:s');

        // Insère une nouvelle entrée dans la table logs
        $log_stmt = $pdo->prepare('INSERT INTO logs (user_id, user_name, timestamp) VALUES (:user_id, :user_name, :timestamp)');
        $log_stmt->bindParam(':user_id', $user_id);
        $log_stmt->bindParam(':user_name', $user_name);
        $log_stmt->bindParam(':timestamp', $timestamp);
        $log_stmt->execute();
    }
} catch (PDOException $e) {
    echo "Erreur lors de l'enregistrement du log : " . $e->getMessage();
}

?>