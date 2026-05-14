<?php
session_start();
require_once 'connbdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($name === '' || $password === '') {
        $_SESSION['erreur'] = 'Tous les champs sont requis.';
        header('Location: connexion.php');
        exit();
    }

    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE nom = :name');
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['nom'];

            if (isset($user['role']) && $user['role'] == 'admin') {
                $_SESSION['role'] = true;
                header('Location: Administrateur.php');
                exit();
            } else {
                $_SESSION['role'] = false;
                header('Location: Utilisateur.php');
                exit();
            }
        } else {
            $_SESSION['erreur'] = 'Nom d\'utilisateur ou mot de passe incorrect.';
            header('Location: connexion.php');
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['erreur'] = 'Erreur lors de la connexion : ' . $e->getMessage();
        header('Location: connexion.php');
        exit();
    }
}

$message = isset($_SESSION['erreur']) ? $_SESSION['erreur'] : (isset($_SESSION['message']) ? $_SESSION['message'] : '');
unset($_SESSION['erreur'], $_SESSION['message']);

try {
    $current_user = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE nom = :name');
    $stmt->bindParam(':name', $current_user);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $user_id = $user['id'];
        $user_name = $user['nom'];
        $timestamp = date('Y-m-d H:i:s');

        $log_stmt = $pdo->prepare('INSERT INTO logs (user_id, user_name, timestamp) VALUES (:user_id, :user_name, :timestamp)');
        $log_stmt->bindParam(':user_id', $user_id);
        $log_stmt->bindParam(':user_name', $user_name);
        $log_stmt->bindParam(':timestamp', $timestamp);
        $log_stmt->execute();
    }
} catch (PDOException $e) {
    // silence log errors on login page
}
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

    <nav class="topnav">
        <a href="index.php" class="nav-btn">Accueil</a>
    </nav>

    <h1>Connexion</h1>

    <?php if ($message): ?>
        <p class="erreur-msg"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="POST" action="connexion.php">
        <div class="formulaire">
            <input type="text" name="name" placeholder="Nom d'utilisateur" id="name"
                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : ''; ?>">
            <input type="password" name="password" placeholder="Mot de passe" id="password">
            <input type="submit" value="Se connecter" class="btn-primary">
        </div>
    </form>

</body>
</html>
