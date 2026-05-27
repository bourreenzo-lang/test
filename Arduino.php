<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['casier']) && isset($_POST['site'])) {

    $site   = $_POST['site'];
    $casier = $_POST['casier'];

    if (!in_array($site, ['A', 'B']) || !in_array($casier, ['1', '2'])) {
        die("Paramètres invalides.");
    }

    require_once 'connbdd.php';

    // Récupération du badge_uid de l'utilisateur connecté
    $badge_uid = null;
    try {
        $bstmt = $pdo->prepare('SELECT badge_uid FROM users WHERE id = :id');
        $bstmt->execute([':id' => $_SESSION['user_id']]);
        $row = $bstmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $badge_uid = $row['badge_uid'];
        }
    } catch (PDOException $e) {
        // non bloquant
    }

    // Enregistrement dans casier_logs
    try {
        $log = $pdo->prepare(
            'INSERT INTO casier_logs (user_id, user_name, badge_uid, site, casier)
             VALUES (:user_id, :user_name, :badge_uid, :site, :casier)'
        );
        $log->execute([
            ':user_id'   => $_SESSION['user_id'],
            ':user_name' => $_SESSION['user_name'],
            ':badge_uid' => $badge_uid,
            ':site'      => $site,
            ':casier'    => (int) $casier,
        ]);
    } catch (PDOException $e) {
        // non bloquant
    }

    $commande = "OUVRIR_SITE" . $site . "_CASIER" . $casier . "\n";

    $port = fopen("COM4", "w");

    if ($port) {
        fwrite($port, $commande);
        fclose($port);
        echo "Commande envoyée à l'Arduino : " . htmlspecialchars($commande, ENT_QUOTES, 'UTF-8');
    } else {
        echo "Erreur : impossible d'ouvrir le port COM.";
    }

} else {
    header('Location: Utilisateur.php');
    exit();
}
?>

<br>
<a href="Casiers.php?site=<?php echo htmlspecialchars($site, ENT_QUOTES, 'UTF-8'); ?>">← Retour aux casiers</a>
