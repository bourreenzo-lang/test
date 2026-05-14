<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création de compte</title>
    <link rel="stylesheet" href="creecompte.css">
</head>
<body>
    <h1>Crée un compte</h1>
    <!-- Formulaire de création de compte -->

    <form method="POST" action="creecompte.php">
    <div class="formulaire">
        <input type="text" name="name" placeholder="Name" id="name">
        <input type="email" name="email" placeholder="Email" id="email">
        <input type="password" name="password" placeholder="Password" id="password">
        
        <input href="Utilisateur.php" type="submit" value="Login">
    </div>
    
</body>
</html>

<?php 
include 'connbdd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    // Vérifie que les champs ne sont pas vides
    if (!empty($_POST["name"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {

        // Récupère les données du formulaire
        $name = $_POST["name"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT); 

        // Prépare la requête d'insertion
        $stmt = $pdo->prepare("INSERT INTO users (nom, email, password) VALUES (:name, :email, :password)");
        
        // Lie les paramètres à la requête préparée
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        // Exécute la requête et gère les erreurs
        try {
            $stmt->execute();
            echo "Utilisateur créé avec succès.";
            // Redirige vers la page de connexion après la création du compte
            header("Location: connexion.php");
        } catch (PDOException $e) {
            echo "Erreur lors de la création de l'utilisateur: " . $e->getMessage();
        }
        

    }
}
?>
