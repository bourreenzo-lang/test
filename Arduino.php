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
