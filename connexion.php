<?php
// démarre la session PHP
session_start();
// on inclut la connexion BDD
require_once 'connbdd.php';

// traitement du form seulement si POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // récup + nettoyage des champs
    $name     = isset($_POST['name'])     ? trim($_POST['name'])     : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';

    // champs vides => erreur
    if ($name === '' || $password === '') {
        $_SESSION['erreur'] = 'Tous les champs sont requis.';
        header('Location: connexion.php');
        exit();
    }
    try {
        // on cherche l'user par son nom
        $stmt = $pdo->prepare('SELECT * FROM users WHERE nom = :name');
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // si l'user existe et que le mdp est bon
        if ($user && password_verify($password, $user['password'])) {
            // on stocke l'id et le nom en session
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['nom'];

            // on log la connexion dans la table logs
            try {
                $log_stmt = $pdo->prepare('INSERT INTO logs (user_id, user_name, timestamp) VALUES (:user_id, :user_name, :timestamp)');
                $log_stmt->execute([
                    ':user_id'   => $user['id'],
                    ':user_name' => $user['nom'],
                    ':timestamp' => date('Y-m-d H:i:s'),
                ]);
            } catch (PDOException $e) {
                // si le log plante on s'en fout, pas bloquant
            }

            // redirection selon le rôle de l'user
            if (isset($user['role']) && $user['role'] == 'admin') {
                $_SESSION['role'] = true;
                header('Location: Administrateur.php'); // admin => tableau de bord admin
                exit();
            } else {
                $_SESSION['role'] = false;
                header('Location: Utilisateur.php'); // user classique => espace user
                exit();
            }
        } else {
            // mauvais identifiants
            $_SESSION['erreur'] = 'Nom d\'utilisateur ou mot de passe incorrect.';
            header('Location: connexion.php');
            exit();
        }

    } catch (PDOException $e) {
        // erreur BDD inattendue
        $_SESSION['erreur'] = 'Erreur lors de la connexion : ' . $e->getMessage();
        header('Location: connexion.php');
        exit();
    }
}

// recup le message d'erreur ou de succès à afficher sur la page
$message = isset($_SESSION['erreur']) ? $_SESSION['erreur'] : (isset($_SESSION['message']) ? $_SESSION['message'] : '');
unset($_SESSION['erreur'], $_SESSION['message']); // on vide apres lecture
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="connexion.css">
</head>
<body>

    <!-- barre de nav en haut à droite -->
    <nav class="topnav">
        <a href="index.php" class="nav-btn">Accueil</a>
    </nav>

    <h1>Connexion</h1>

    <!-- affiche le message d'erreur si y'en a un -->
    <?php if ($message): ?>
        <p class="erreur-msg"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <!-- formulaire de connexion -->
    <form method="POST" action="connexion.php">
        <div class="formulaire">
            <!-- champ nom, on remet la valeur saisie si erreur -->
            <input type="text" name="name" placeholder="Nom d'utilisateur" id="name"
                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            <!-- champ mdp, jamais de valeur par défaut -->
            <input type="password" name="password" placeholder="Mot de passe" id="password">
            <input type="submit" value="Se connecter" class="btn-primary">
        </div>
    </form>

</body>
</html>
