<?php
session_start();

// si pas connecté, on redirige vers la co
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// on traite seulement si POST avec les bons params
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['casier']) && isset($_POST['site'])) {

    $site   = $_POST['site'];
    $casier = $_POST['casier'];

    // vérif stricte : site = A ou B, casier = 1 ou 2 uniquement
    if (!in_array($site, ['A', 'B']) || !in_array($casier, ['1', '2'])) {
        die("Paramètres invalides.");
    }

    require_once 'connbdd.php';

    // on récup le badge_uid de l'user co depuis la BDD
    $badge_uid = null;
    try {
        $bstmt = $pdo->prepare('SELECT badge_uid FROM users WHERE id = :id');
        $bstmt->execute([':id' => $_SESSION['user_id']]);
        $row = $bstmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $badge_uid = $row['badge_uid']; // peut être null si pas de badge associé
        }
    } catch (PDOException $e) {
        // pas bloquant, on continue même sans badge_uid
    }

    // on enregistre l'ouverture dans casier_logs pour les stats admin
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
        // pas bloquant non plus, l'ouverture doit quand même fonctionner
    }

    // construction de la commande à envoyer à l'Arduino via le port série
    $commande = "OUVRIR_SITE" . $site . "_CASIER" . $casier . "\n";

    // ouverture du port COM4 (port série Arduino)
    $port = fopen("COM4", "w");

    if ($port) {
        // envoie la commande et ferme le port
        fwrite($port, $commande);
        fclose($port);
        echo "Commande envoyée à l'Arduino : " . htmlspecialchars($commande, ENT_QUOTES, 'UTF-8');
    } else {
        // le port COM4 est inaccessible (Arduino débranché ?)
        echo "Erreur : impossible d'ouvrir le port COM.";
    }

} else {
    // accès direct sans POST => retour espace user
    header('Location: Utilisateur.php');
    exit();
}
?>

<br>
<!-- lien retour vers la liste des casiers du même site -->
<a href="Casiers.php?site=<?php echo htmlspecialchars($site, ENT_QUOTES, 'UTF-8'); ?>">← Retour aux casiers</a>
