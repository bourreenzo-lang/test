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
    // session requise dès le début du corps car on a le HTML avant
    session_start();
    // si l'user est pas connecté, on le vire vers la page de co
    if (!isset($_SESSION['user_id'])) {
        header('Location: connexion.php');
        exit();
    }
    // récup du nom d'utilisateur pour l'afficher, avec fallback "Utilisateur"
    $username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'Utilisateur';
    ?>

    <!-- nav : accueil + déconnexion -->
    <nav class="topnav">
        <a href="index.php" class="nav-btn">Accueil</a>
        <a href="deconnexion.php" class="nav-btn nav-btn-danger">Déconnexion</a>
    </nav>

    <!-- accueil perso avec le prénom de l'user -->
    <h1>Bienvenue <?php echo $username; ?></h1>

    <!-- petite carte d'info -->
    <div class="text">
        <p>Vous êtes connecté à votre compte.</p>
        <p>Choisissez un site pour accéder à ses casiers.</p>
    </div>

    <!-- les 2 boutons pour choisir le site A ou B -->
    <div class="form-sites">
        <a href="Casiers.php?site=A"><button class="btn-casierA">🏢 Site A</button></a>
        <a href="Casiers.php?site=B"><button class="btn-casierB">🏢 Site B</button></a>
    </div>

</body>
</html>
