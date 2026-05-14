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
        $stmt = $conn->prepare("INSERT INTO users (nom, email, password) VALUES (:name, :email, :password)");
        
        // Lie les paramètres à la requête préparée
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);

        // Exécute la requête et gère les erreurs
        try {
            $stmt->execute();
            echo "Utilisateur créé avec succès.";
            // Redirige vers la page de connexion après la création du compte
            header("Location: log.php");
        } catch (PDOException $e) {
            echo "Erreur lors de la création de l'utilisateur: " . $e->getMessage();
        }
        

    }
}
?>