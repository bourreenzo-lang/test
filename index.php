<?php
// démarre la session pour pouvoir lire les messages flash (succes/erreur)
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de casier</title>
    <!-- feuille de style de la page d'accueil -->
    <link rel="stylesheet" href="index.css">
</head>
<!-- body principal de la page d'accueil -->
<body class="page-index">

    <h1>Gestion de casier</h1>

    <?php if(isset($_SESSION['message'])): ?>
        <!-- message de succès (ex: compte créé) -->
        <div class="message succes">
            <?= htmlspecialchars($_SESSION['message']); ?>
            <?php unset($_SESSION['message']); // on efface apres affichage ?>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['erreur'])): ?>
        <!-- message d'erreur venu d'une autre page -->
        <div class="message erreur">
            <?= htmlspecialchars($_SESSION['erreur']); ?>
            <?php unset($_SESSION['erreur']); // pareil, on vide apres ?>
        </div>
    <?php endif; ?>

    <!-- boutons principaux : connexion ou creation de compte -->
    <div class="formulaire">
        <a href="connexion.php"><button>Se connecter</button></a>
        <a href="creecompte.php"><button>Créer un compte</button></a>
    </div>

</body>
</html>
