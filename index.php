<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de casier</title>
    <link rel="stylesheet" href="index.css">
</head>
<body class="page-index light">

    <h1>Gestion de casier</h1>

    <?php if(isset($_SESSION['message'])): ?>
        <div class="message succes">
            <?= htmlspecialchars($_SESSION['message']); ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['erreur'])): ?>
        <div class="message erreur">
            <?= htmlspecialchars($_SESSION['erreur']); ?>
            <?php unset($_SESSION['erreur']); ?>
        </div>
    <?php endif; ?>

    <div class="formulaire">
        <a href="connexion.php"><button>Se connecter</button></a>
        <a href="creecompte.php"><button>Créer un compte</button></a>
    </div>

</body>
</html>