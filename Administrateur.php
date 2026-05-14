<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord Administrateur</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <?php
    require_once 'connbdd.php';
    session_start();
    $username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'Utilisateur';
    ?>

    <header class="topbar">
        <div class="topbar-content">
            <div>
                <h1>Bonjour, <?php echo $username; ?> !</h1>
                <p class="subtitle">Bienvenue sur votre espace administrateur sécurisé.</p>
            </div>
            <div class="actions">
                <a href="index.php" class="btn">Accueil</a>
                <a href="deconnexion.php" class="btn btn-danger">Déconnexion</a>
            </div>
        </div>
    </header>

    <main class="container">
        <section class="dashboard-cards">
            <article class="card">
                <h2>Utilisateurs</h2>
                <p>Gérez les comptes, consultez les sessions et modifiez les privilèges.</p>
                <div class="card-actions">
                    <a href="#liste-utilisateurs" class="btn-card">Voir les utilisateurs</a>
                </div>
            </article>
            <article class="card">
                <h2>Logs</h2>
                <p>Suivi des connexions récentes et sécurité en temps réel.</p>
            </article>
            <article class="card">
                <h2>Paramètres</h2>
                <p>Accédez aux paramètres généraux du site.</p>
            </article>
        </section>

        <section class="historique" id="liste-utilisateurs">
            <h2>Liste des utilisateurs</h2>
            <p>Voici les comptes enregistrés sur le site :</p>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    try {
                        $stmt = $pdo->query('SELECT id, nom, email, role FROM users ORDER BY id DESC');
                        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td>' . htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td>' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td>' . htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '</tr>';
                        }
                    } catch (PDOException $e) {
                        echo '<tr><td colspan="4" class="error">Erreur : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> SiteprojetEnzo - Espace Administrateur</p>
    </footer>
</body>
</html>
