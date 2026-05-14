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
    if (!isset($_SESSION['user_id'])) {
        header('Location: connexion.php');
        exit();
    }

    $site = isset($_GET['site']) && in_array($_GET['site'], ['A', 'B']) ? $_GET['site'] : null;

    if (!$site) {
        header('Location: Utilisateur.php');
        exit();
    }
    ?>

    <nav class="topnav">
        <a href="Utilisateur.php" class="nav-btn">← Retour</a>
        <a href="deconnexion.php" class="nav-btn nav-btn-danger">Déconnexion</a>
    </nav>

    <h1>Site <?php echo htmlspecialchars($site, ENT_QUOTES, 'UTF-8'); ?></h1>

    <div class="text">
        <p>Choisissez le casier que vous souhaitez ouvrir.</p>
    </div>

    <form method="POST" action="Arduino.php">
        <input type="hidden" name="site" value="<?php echo htmlspecialchars($site, ENT_QUOTES, 'UTF-8'); ?>">
        <button class="btn-casierA" name="casier" value="1">🗄️ Casier 1</button>
        <button class="btn-casierB" name="casier" value="2">🗄️ Casier 2</button>
    </form>

</body>
</html>
