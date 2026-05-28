<?php
// session dispo pour les messages flash
session_start();

// variables pour le message affiché à l'user
$message      = '';
$message_type = '';

// traitement uniquement si le form est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // récup et nettoyage des champs du form
    $name     = isset($_POST['name'])     ? trim($_POST['name'])     : '';
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';

    // validations de base
    if ($name === '' || $email === '' || $password === '') {
        $message      = 'Tous les champs sont requis.';
        $message_type = 'erreur';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // format email invalide
        $message      = 'Adresse e-mail invalide.';
        $message_type = 'erreur';
    } elseif (strlen($password) < 6) {
        // mdp trop court
        $message      = 'Le mot de passe doit contenir au moins 6 caractères.';
        $message_type = 'erreur';
    } else {
        // tout est ok, on crée le compte
        require_once 'connbdd.php';
        $hashed = password_hash($password, PASSWORD_DEFAULT); // on hash le mdp avant stockage
        $stmt = $pdo->prepare("INSERT INTO users (nom, email, password) VALUES (:name, :email, :password)");
        $stmt->bindParam(':name',     $name);
        $stmt->bindParam(':email',    $email);
        $stmt->bindParam(':password', $hashed);

        try {
            $stmt->execute();
            // compte créé avec succès, on redirige vers la co
            $_SESSION['message'] = 'Compte créé avec succès. Vous pouvez vous connecter.';
            header('Location: connexion.php');
            exit();
        } catch (PDOException $e) {
            // code 23000 = doublon (nom ou email déjà utilisé)
            if ($e->getCode() == 23000) {
                $message = 'Ce nom d\'utilisateur ou cet e-mail est déjà utilisé.';
            } else {
                $message = 'Erreur lors de la création du compte.';
            }
            $message_type = 'erreur';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte</title>
    <link rel="stylesheet" href="creecompte.css">
</head>
<body>

    <!-- nav avec retour accueil -->
    <nav class="topnav">
        <a href="index.php" class="nav-btn">Accueil</a>
    </nav>

    <h1>Créer un compte</h1>

    <!-- message d'erreur ou de succès si y'en a un -->
    <?php if ($message): ?>
        <p class="<?php echo $message_type; ?>-msg">
            <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
        </p>
    <?php endif; ?>

    <!-- form de création : nom, email, mdp -->
    <form method="POST" action="creecompte.php">
        <div class="formulaire">
            <!-- on remet les valeurs saisies en cas d'erreur (sauf mdp) -->
            <input type="text"     name="name"     placeholder="Nom d'utilisateur" id="name"
                value="<?php echo isset($_POST['name'])  ? htmlspecialchars($_POST['name'],  ENT_QUOTES, 'UTF-8') : ''; ?>">
            <input type="email"    name="email"    placeholder="Email" id="email"
                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            <input type="password" name="password" placeholder="Mot de passe (6 caractères min.)" id="password">
            <input type="submit" value="Créer le compte" class="btn-primary">
        </div>
    </form>

</body>
</html>
