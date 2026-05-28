<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casiers</title>
    <link rel="stylesheet" href="util.css">
</head>
<body>

    <?php
    session_start();
    // vérif que l'user est bien connecté
    if (!isset($_SESSION['user_id'])) {
        header('Location: connexion.php');
        exit();
    }

    // on récup le param "site" dans l'URL (A ou B uniquement)
    $site = isset($_GET['site']) && in_array($_GET['site'], ['A', 'B']) ? $_GET['site'] : null;

    // si le site est invalide ou absent, retour à l'espace user
    if (!$site) {
        header('Location: Utilisateur.php');
        exit();
    }
    ?>

    <!-- nav : retour à la liste des sites + déco -->
    <nav class="topnav">
        <a href="Utilisateur.php" class="nav-btn">← Retour</a>
        <a href="deconnexion.php" class="nav-btn nav-btn-danger">Déconnexion</a>
    </nav>

    <!-- affiche le nom du site (A ou B) -->
    <h1>Site <?php echo htmlspecialchars($site, ENT_QUOTES, 'UTF-8'); ?></h1>

    <div class="text">
        <p>Choisissez le casier que vous souhaitez ouvrir.</p>
    </div>

    <!-- form qui envoie le site + le numéro de casier à Arduino.php -->
    <form method="POST" action="Arduino.php">
        <!-- champ caché pour passer le site au script Arduino -->
        <input type="hidden" name="site" value="<?php echo htmlspecialchars($site, ENT_QUOTES, 'UTF-8'); ?>">
        <!-- boutons casier 1 et 2, la valeur envoyée est le numéro -->
        <button class="btn-casierA" name="casier" value="1">🗄️ Casier 1</button>
        <button class="btn-casierB" name="casier" value="2">🗄️ Casier 2</button>
    </form>

</body>
</html>
