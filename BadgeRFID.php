<?php
// ce script est appelé par le bridge Python quand un badge est scanné
// il vérifie si le badge est connu et enregistre le passage
require_once 'connbdd.php';

// le badge_uid est passé en GET par le script Python
if (!isset($_GET['badge_uid'])) {
    echo "ERREUR"; // pas de badge_uid => on répond erreur
    exit();
}

$badge_uid = trim($_GET['badge_uid']);

// on cherche l'user associé à ce badge dans la BDD
$stmt = $pdo->prepare("SELECT id, nom FROM users WHERE badge_uid = :badge_uid");
$stmt->bindParam(':badge_uid', $badge_uid);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    // badge reconnu : on logge le passage dans badge_scans
    $insert = $pdo->prepare(
        "INSERT INTO badge_scans (badge_uid, user_id, user_name, role)
         SELECT :badge_uid, id, nom, role FROM users WHERE badge_uid = :badge_uid2"
    );
    $insert->bindParam(':badge_uid',  $badge_uid);
    $insert->bindParam(':badge_uid2', $badge_uid);
    $insert->execute();

    // réponse OK avec le nom de l'user pour affichage Arduino
    echo "OK;" . $user['nom'];
} else {
    // badge inconnu => accès refusé
    echo "REFUS";
}
?>
