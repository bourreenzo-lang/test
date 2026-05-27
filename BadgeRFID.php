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
    // Enregistrement du passage dans badge_scans
    $insert = $pdo->prepare(
        "INSERT INTO badge_scans (badge_uid, user_id, user_name, role)
         SELECT :badge_uid, id, nom, role FROM users WHERE badge_uid = :badge_uid2"
    );
    $insert->bindParam(':badge_uid',  $badge_uid);
    $insert->bindParam(':badge_uid2', $badge_uid);
    $insert->execute();

    echo "OK;" . $user['nom'];
} else {
    echo "REFUS";
}
?>