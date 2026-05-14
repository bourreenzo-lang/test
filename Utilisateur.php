<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace utilisateur</title>
    <link rel="stylesheet" href="util.css">
</head>
<body>

    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: connexion.php');
        exit();
    }
    $username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'Utilisateur';
    ?>

    <nav class="topnav">
        <a href="index.php" class="nav-btn">Accueil</a>
        <a href="deconnexion.php" class="nav-btn nav-btn-danger">Déconnexion</a>
    </nav>

    <h1>Bienvenue <?php echo $username; ?></h1>

    <div class="text">
        <p>Vous êtes connecté à votre compte.</p>
        <p>Choisissez un site pour accéder à ses casiers.</p>
    </div>

    <div class="form-sites">
        <a href="Casiers.php?site=A"><button class="btn-casierA">🏢 Site A</button></a>
        <a href="Casiers.php?site=B"><button class="btn-casierB">🏢 Site B</button></a>
    </div>

</body>
</html>
