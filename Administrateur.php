<?php
session_start();
// vérif que l'user est co ET qu'il est bien admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== true) {
    header('Location: connexion.php');
    exit();
}
require_once 'connbdd.php';
date_default_timezone_set('Europe/Paris'); // fuseau horaire France
$username = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name'], ENT_QUOTES, 'UTF-8') : 'Utilisateur';
?>
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
    // traitement du changement de rôle soumis via le form "Paramètres"
    $role_msg      = '';
    $role_msg_type = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_role'], $_POST['user_id'], $_POST['new_role'])) {
        $allowed_roles = ['admin', 'user', 'technicien']; // rôles autorisés
        $new_role = $_POST['new_role'];
        $user_id  = (int) $_POST['user_id'];
        // on vérifie que le rôle choisi est valide et que l'id est positif
        if (in_array($new_role, $allowed_roles, true) && $user_id > 0) {
            try {
                $upd = $pdo->prepare('UPDATE users SET role = :role WHERE id = :id');
                $upd->execute([':role' => $new_role, ':id' => $user_id]);
                $role_msg      = 'Rôle mis à jour avec succès.';
                $role_msg_type = 'success';
            } catch (PDOException $e) {
                $role_msg      = 'Erreur : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
                $role_msg_type = 'error';
            }
        } else {
            // données POST invalides ou bidouillées
            $role_msg      = 'Données invalides.';
            $role_msg_type = 'error';
        }
    }
    ?>

    <!-- topbar avec bonjour + boutons -->
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

        <!-- 4 cartes de navigation rapide vers les sections -->
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
                <div class="card-actions">
                    <a href="#historique-logs" class="btn-card">Voir les logs</a>
                </div>
            </article>
            <article class="card">
                <h2>Casiers</h2>
                <p>Historique des ouvertures de casiers par badge RFID.</p>
                <div class="card-actions">
                    <a href="#historique-casiers" class="btn-card">Voir les ouvertures</a>
                </div>
            </article>
            <article class="card">
                <h2>Paramètres</h2>
                <p>Gestion des rôles utilisateurs et statistiques de la base de données.</p>
                <div class="card-actions">
                    <a href="#parametres" class="btn-card">Voir les paramètres</a>
                </div>
            </article>
        </section>

        <!-- liste de tous les users enregistrés -->
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
                        // on récup tous les users, du plus récent au plus ancien
                        $stmt = $pdo->query('SELECT id, nom, email, role FROM users ORDER BY id DESC');
                        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($user['id'],    ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td>' . htmlspecialchars($user['nom'],   ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td>' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . '</td>';
                            echo '<td>' . htmlspecialchars($user['role'],  ENT_QUOTES, 'UTF-8') . '</td>';
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

        <!-- historique des connexions (table logs) -->
        <section class="historique" id="historique-logs">
            <h2>Historique des logs</h2>
            <p>Connexions enregistrées sur le site, de la plus récente à la plus ancienne.</p>
            <div class="table-wrapper">
                <!-- barre de recherche en temps réel + compteur d'entrées -->
                <div class="logs-toolbar">
                    <input type="text" id="logs-search" placeholder="Rechercher un utilisateur…" class="logs-search-input">
                    <span id="logs-count" class="logs-count"></span>
                </div>
                <table id="logs-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Utilisateur</th>
                            <th>ID utilisateur</th>
                            <th>Date &amp; heure</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    try {
                        // on limite à 200 entrées pour pas surcharger la page
                        $logs_stmt = $pdo->query('SELECT id, user_id, user_name, timestamp FROM logs ORDER BY timestamp DESC LIMIT 200');
                        $logs = $logs_stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (empty($logs)) {
                            echo '<tr><td colspan="4" class="logs-empty">Aucun log enregistré pour le moment.</td></tr>';
                        } else {
                            foreach ($logs as $log) {
                                // formatage de la date en heure Paris
                                $dt        = new DateTime($log['timestamp'], new DateTimeZone('Europe/Paris'));
                                $formatted = $dt->format('d/m/Y à H:i:s');
                                echo '<tr>';
                                echo '<td class="log-id">'  . htmlspecialchars($log['id'],        ENT_QUOTES, 'UTF-8') . '</td>';
                                echo '<td class="log-name"><span class="log-badge">' . htmlspecialchars($log['user_name'], ENT_QUOTES, 'UTF-8') . '</span></td>';
                                echo '<td class="log-uid">'  . htmlspecialchars($log['user_id'],  ENT_QUOTES, 'UTF-8') . '</td>';
                                echo '<td class="log-time">' . $formatted . '</td>';
                                echo '</tr>';
                            }
                        }
                    } catch (PDOException $e) {
                        echo '<tr><td colspan="4" class="error">Erreur : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- historique des ouvertures de casiers via badge RFID -->
        <section class="historique" id="historique-casiers">
            <h2>Historique des ouvertures de casiers</h2>
            <p>Toutes les ouvertures enregistrées, de la plus récente à la plus ancienne.</p>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Technicien</th>
                            <th>Badge UID</th>
                            <th>Site</th>
                            <th>Casier</th>
                            <th>Date &amp; heure</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    try {
                        // idem, limite 200 pour les perfs
                        $casier_stmt = $pdo->query(
                            'SELECT id, user_name, badge_uid, site, casier, opened_at
                             FROM casier_logs
                             ORDER BY opened_at DESC
                             LIMIT 200'
                        );
                        $casier_logs = $casier_stmt->fetchAll(PDO::FETCH_ASSOC);
                        if (empty($casier_logs)) {
                            echo '<tr><td colspan="6" class="logs-empty">Aucune ouverture enregistrée pour le moment.</td></tr>';
                        } else {
                            foreach ($casier_logs as $cl) {
                                $dt  = new DateTime($cl['opened_at'], new DateTimeZone('Europe/Paris'));
                                $fmt = $dt->format('d/m/Y à H:i:s');
                                // si pas de badge_uid on met un tiret
                                $uid = isset($cl['badge_uid']) ? $cl['badge_uid'] : '—';
                                echo '<tr>';
                                echo '<td class="log-id">'  . htmlspecialchars($cl['id'],        ENT_QUOTES, 'UTF-8') . '</td>';
                                echo '<td class="log-name"><span class="log-badge">' . htmlspecialchars($cl['user_name'], ENT_QUOTES, 'UTF-8') . '</span></td>';
                                echo '<td class="log-uid">' . htmlspecialchars($uid,              ENT_QUOTES, 'UTF-8') . '</td>';
                                echo '<td class="log-uid">Site ' . htmlspecialchars($cl['site'],  ENT_QUOTES, 'UTF-8') . '</td>';
                                echo '<td class="log-uid">Casier ' . (int)$cl['casier'] . '</td>';
                                echo '<td class="log-time">' . $fmt . '</td>';
                                echo '</tr>';
                            }
                        }
                    } catch (PDOException $e) {
                        echo '<tr><td colspan="6" class="error">Erreur : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- section paramètres : gestion des rôles + stats BDD -->
        <section class="historique" id="parametres">
            <h2>Paramètres</h2>
            <p>Gestion des rôles et informations sur la base de données.</p>

            <!-- message de retour après changement de rôle -->
            <?php if ($role_msg): ?>
                <div class="param-msg param-msg--<?php echo $role_msg_type; ?>">
                    <?php echo $role_msg; ?>
                </div>
            <?php endif; ?>

            <!-- tableau pour modifier le rôle de chaque user -->
            <div class="param-block">
                <h3 class="param-title">Gestion des rôles</h3>
                <p class="param-desc">Modifiez le rôle de chaque utilisateur via le menu déroulant.</p>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Rôle actuel</th>
                                <th>Changer le rôle</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        try {
                            // on liste tous les users par ID croissant
                            $stmt2 = $pdo->query('SELECT id, nom, email, role FROM users ORDER BY id ASC');
                            while ($u = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                                // échappement des valeurs pour l'affichage HTML
                                $uid   = htmlspecialchars($u['id'],    ENT_QUOTES, 'UTF-8');
                                $unom  = htmlspecialchars($u['nom'],   ENT_QUOTES, 'UTF-8');
                                $umail = htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8');
                                $urole = htmlspecialchars($u['role'],  ENT_QUOTES, 'UTF-8');
                                // classe CSS du badge selon le rôle
                                $badge_class = $u['role'] === 'admin' ? 'role-admin' : ($u['role'] === 'technicien' ? 'role-technicien' : 'role-user');
                                echo '<tr>';
                                echo '<td class="log-id">' . $uid . '</td>';
                                echo '<td>' . $unom . '</td>';
                                echo '<td class="log-uid">' . $umail . '</td>';
                                echo '<td><span class="role-badge ' . $badge_class . '">' . $urole . '</span></td>';
                                echo '<td>';
                                // petit form inline pour changer le rôle sans quitter la page
                                echo '<form method="POST" action="#parametres" class="role-form">';
                                echo '<input type="hidden" name="update_role" value="1">';
                                echo '<input type="hidden" name="user_id" value="' . $uid . '">';
                                echo '<select name="new_role" class="role-select">';
                                // option sélectionnée selon le rôle actuel de l'user
                                echo '<option value="user"'        . ($u['role'] === 'user'        ? ' selected' : '') . '>user</option>';
                                echo '<option value="technicien"' . ($u['role'] === 'technicien' ? ' selected' : '') . '>technicien</option>';
                                echo '<option value="admin"'      . ($u['role'] === 'admin'      ? ' selected' : '') . '>admin</option>';
                                echo '</select>';
                                echo ' <button type="submit" class="btn-role">Appliquer</button>';
                                echo '</form>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } catch (PDOException $e) {
                            echo '<tr><td colspan="5" class="error">Erreur : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- stats de la BDD : tables, nb de lignes, moteur, etc. -->
            <div class="param-block">
                <h3 class="param-title">Base de données</h3>
                <p class="param-desc">Tables présentes et nombre d'enregistrements.</p>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Table</th>
                                <th>Nb lignes</th>
                                <th>Moteur</th>
                                <th>Encodage</th>
                                <th>Dernière mise à jour</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        try {
                            // on récup le nom de la BDD courante
                            $db_name = $pdo->query('SELECT DATABASE()')->fetchColumn();
                            // requête sur information_schema pour avoir les stats des tables
                            $tbl_stmt = $pdo->prepare(
                                'SELECT TABLE_NAME, TABLE_ROWS, ENGINE, TABLE_COLLATION, UPDATE_TIME
                                 FROM information_schema.TABLES
                                 WHERE TABLE_SCHEMA = :db
                                 ORDER BY TABLE_NAME ASC'
                            );
                            $tbl_stmt->execute([':db' => $db_name]);
                            $tables = $tbl_stmt->fetchAll(PDO::FETCH_ASSOC);
                            if (empty($tables)) {
                                echo '<tr><td colspan="5" class="logs-empty">Aucune table trouvée.</td></tr>';
                            } else {
                                foreach ($tables as $tbl) {
                                    // formatage de la date de dernière maj
                                    if (!empty($tbl['UPDATE_TIME'])) {
                                        $dt_upd = new DateTime($tbl['UPDATE_TIME'], new DateTimeZone('Europe/Paris'));
                                        $upd = $dt_upd->format('d/m/Y H:i');
                                    } else {
                                        $upd = '—'; // pas de date dispo (table jamais modifiée)
                                    }
                                    $engine    = !empty($tbl['ENGINE'])          ? $tbl['ENGINE']          : '—';
                                    $collation = !empty($tbl['TABLE_COLLATION']) ? $tbl['TABLE_COLLATION'] : '—';
                                    echo '<tr>';
                                    echo '<td><strong>' . htmlspecialchars($tbl['TABLE_NAME'], ENT_QUOTES, 'UTF-8') . '</strong></td>';
                                    echo '<td class="log-uid">' . (int)$tbl['TABLE_ROWS'] . '</td>';
                                    echo '<td class="log-uid">' . htmlspecialchars($engine,    ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td class="log-uid">' . htmlspecialchars($collation, ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td class="log-time">' . $upd . '</td>';
                                    echo '</tr>';
                                }
                            }
                        } catch (PDOException $e) {
                            echo '<tr><td colspan="5" class="error">Erreur : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</td></tr>';
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    <script>
    // filtrage en temps réel des logs par nom d'user
    const searchInput = document.getElementById('logs-search');
    const logsCount   = document.getElementById('logs-count');
    const tbody       = document.querySelector('#logs-table tbody');

    // met à jour le compteur d'entrées visibles
    function updateCount() {
        const visible = tbody.querySelectorAll('tr:not([style*="display: none"])').length;
        logsCount.textContent = visible + ' entrée' + (visible > 1 ? 's' : '');
    }
    updateCount(); // appel initial au chargement

    // à chaque frappe dans le champ de recherche, on filtre les lignes
    searchInput.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        tbody.querySelectorAll('tr').forEach(function (row) {
            const name = row.querySelector('.log-name');
            if (!name) return;
            // on cache les lignes qui ne correspondent pas
            row.style.display = name.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
        updateCount();
    });
    </script>

    <!-- footer avec l'année automatique -->
    <footer class="footer">
        <p>&copy; <?php echo date('Y'); ?> SiteprojetEnzo - Espace Administrateur</p>
    </footer>
</body>
</html>
