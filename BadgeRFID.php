<?php
require_once 'connbdd.php';

if (!isset($_GET['badge_uid'])) {
    echo "ERREUR";
    exit();
}

$badge_uid = trim($_GET['badge_uid']);

$stmt = $pdo->prepare("SELECT id, nom FROM users WHERE badge_uid = :badge_uid");
$stmt->bindParam(':badge_uid', $badge_uid);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo "OK;" . $user['nom'];
} else {
    echo "REFUS";
}
?>