# 🗄️ Gestion de Casiers — Projet BTS

Application web de gestion de casiers physiques connectés à un Arduino, développée dans le cadre d'un projet BTS.  
Elle permet à des utilisateurs de s'authentifier et d'ouvrir des casiers à distance via une interface web, tandis qu'un administrateur gère les comptes, les rôles et consulte les logs de connexion.

---

## 📋 Fonctionnalités

- **Authentification** : connexion sécurisée avec hachage des mots de passe (`password_hash`)
- **Rôles** : deux niveaux d'accès — `user` et `admin`
- **Espace utilisateur** : choix du site (A ou B) et ouverture de casiers (1 ou 2) via commande série Arduino
- **Espace administrateur** :
  - Liste des utilisateurs
  - Historique des connexions (logs) avec recherche en temps réel
  - Gestion des rôles (promotion/rétrogradation)
  - Statistiques de la base de données
- **Badge RFID** : script Python de liaison Arduino ↔ serveur pour authentification par badge
- **Création de compte** : inscription avec validation des champs et détection des doublons

---

## 🛠️ Stack technique

| Couche | Technologie |
|---|---|
| Backend | PHP 7+ |
| Base de données | MySQL 5.7+ |
| Frontend | HTML5 / CSS3 |
| Matériel | Arduino (port série COM4) |
| Bridge RFID | Python 3 + `pyserial` + `requests` |
| Serveur local | UwAmp / WAMP |

---

## 📁 Structure du projet

```
SiteprojetEnzoV3/
│
├── index.php            # Page d'accueil (connexion / créer un compte)
├── connexion.php        # Authentification + log de connexion
├── creecompte.php       # Création de compte
├── deconnexion.php      # Déconnexion (destruction de session)
│
├── Utilisateur.php      # Espace utilisateur — choix du site
├── Casiers.php          # Choix du casier à ouvrir
├── Arduino.php          # Envoi de la commande au port série Arduino
│
├── Administrateur.php   # Tableau de bord administrateur
├── BadgeRFID.php        # Endpoint PHP pour la validation des badges RFID
│
├── connbdd.php          # Connexion PDO à la base de données
├── bridge_badge.py      # Script Python : Arduino → BadgeRFID.php
├── gestion.casierss.sql # Script SQL de création de la BDD
│
├── index.css
├── connexion.css
├── creecompte.css
├── util.css             # Styles partagés (Utilisateur, Casiers)
└── admin.css            # Styles du tableau de bord admin
```

---

## ⚙️ Installation

### Prérequis

- Serveur local avec PHP 7+ et MySQL (ex : [UwAmp](https://www.uwamp.com/), WAMP, XAMPP)
- Python 3 avec les packages `pyserial` et `requests`
- Arduino connecté sur le port **COM4**

### 1. Cloner le dépôt

```bash
git clone https://github.com/<votre-utilisateur>/SiteprojetEnzoV3.git
```

Placer le dossier dans le répertoire `www` de votre serveur local.

### 2. Créer la base de données

Importer le fichier SQL via phpMyAdmin ou en ligne de commande :

```bash
mysql -u root -p < gestion.casierss.sql
```

> La base de données s'appelle `gestion_casiers`.

### 3. Configurer la connexion BDD

Éditer `connbdd.php` avec vos identifiants :

```php
$host     = 'localhost';
$dbname   = 'gestion_casiers';
$username = 'root';
$password = 'votre_mot_de_passe';
```

### 4. Lancer le site

Accéder à l'application dans votre navigateur :

```
http://localhost/SiteprojetEnzoV3/
```

### 5. Lancer le bridge RFID (optionnel)

Installer les dépendances Python :

```bash
pip install pyserial requests
```

Lancer le script de liaison Arduino :

```bash
python bridge_badge.py
```

> ⚠️ Vérifier que le port `COM4` dans `bridge_badge.py` correspond bien au port de votre Arduino.

---

## 🗃️ Base de données

### Table `users`

| Colonne | Type | Description |
|---|---|---|
| `id` | INT | Identifiant unique |
| `nom` | VARCHAR(100) | Nom d'utilisateur (unique) |
| `email` | VARCHAR(150) | Adresse e-mail (unique) |
| `password` | VARCHAR(255) | Mot de passe haché |
| `role` | ENUM('admin','user') | Rôle de l'utilisateur |
| `created_at` | TIMESTAMP | Date de création |

### Table `logs`

| Colonne | Type | Description |
|---|---|---|
| `id` | INT | Identifiant unique |
| `user_id` | INT | Référence vers `users.id` |
| `user_name` | VARCHAR(100) | Nom de l'utilisateur |
| `action` | VARCHAR(100) | Action effectuée (ex : `connexion`) |
| `ip_address` | VARCHAR(45) | Adresse IP (optionnel) |
| `timestamp` | TIMESTAMP | Date et heure de l'action |

---

## 👤 Comptes par défaut

> Après import du fichier SQL, deux comptes sont disponibles :

| Nom | Rôle | 
|---|---|
| `admin` | admin |
| `leny` | admin |

⚠️ Pensez à changer les mots de passe après la première connexion.

---

## 🔐 Sécurité

- Mots de passe hachés avec `password_hash()` / vérifiés avec `password_verify()`
- Requêtes SQL préparées (protection contre les injections SQL)
- Échappement des sorties HTML avec `htmlspecialchars()` (protection XSS)
- Vérification du rôle admin avant accès au tableau de bord
- Sessions PHP pour la gestion de l'authentification

---

## 🤝 Auteur

Projet réalisé par **Enzo** dans le cadre d'un BTS.
