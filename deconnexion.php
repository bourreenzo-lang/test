<?php
// démarre la session pour pouvoir la détruire
session_start();
// on vide et supprime toute la session (user_id, role, etc.)
session_destroy();
// retour à la page d'accueil
header('Location: index.php');
exit();
?>
