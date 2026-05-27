<?php

function log_connection($pdo, $user_id, $user_name) {
    $dateTime = new DateTime('now', new DateTimeZone('Europe/Paris'));
    $timestamp = $dateTime->format('Y-m-d H:i:s');
    try {
        $log_stmt = $pdo->prepare('INSERT INTO logs (user_id, user_name, timestamp) VALUES (:user_id, :user_name, :timestamp)');
        $log_stmt->bindParam(':user_id', $user_id);
        $log_stmt->bindParam(':user_name', $user_name);
        $log_stmt->bindParam(':timestamp', $timestamp);
        $log_stmt->execute();
    } catch (PDOException $e) {
        error_log("Erreur lors de l'enregistrement du log : " . $e->getMessage());
    }
}

function date_heure_actuelle() {
    $dateTime = new DateTime('now', new DateTimeZone('Europe/Paris'));
    return $dateTime->format('Y-m-d H:i:s');
}


?>