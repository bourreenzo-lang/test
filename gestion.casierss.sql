-- phpMyAdmin SQL Dump
-- version 4.5.4.1
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Lun 11 Mai 2026 à 06:52
-- Version du serveur :  5.7.11
-- Version de PHP :  7.0.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `gestion_casierss`
--

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL DEFAULT 'connexion',
  `ip_address` varchar(45) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `user_name`, `action`, `ip_address`, `timestamp`) VALUES
(1, 2, 'leny', 'connexion', NULL, '2026-04-09 06:36:28'),
(2, 2, 'leny', 'connexion', NULL, '2026-04-09 06:41:45'),
(3, 2, 'leny', 'connexion', NULL, '2026-04-09 06:43:51'),
(4, 2, 'leny', 'connexion', NULL, '2026-04-09 06:43:57'),
(5, 2, 'leny', 'connexion', NULL, '2026-04-09 06:44:42'),
(6, 3, 'Enzo', 'connexion', NULL, '2026-04-27 11:06:34'),
(7, 2, 'leny', 'connexion', NULL, '2026-04-27 11:14:28'),
(8, 2, 'leny', 'connexion', NULL, '2026-04-27 11:46:29'),
(9, 2, 'leny', 'connexion', NULL, '2026-04-27 11:52:41'),
(10, 2, 'leny', 'connexion', NULL, '2026-04-27 13:19:55'),
(11, 2, 'leny', 'connexion', NULL, '2026-04-27 13:26:22'),
(12, 2, 'leny', 'connexion', NULL, '2026-04-27 13:26:32');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'admin', 'admin@site.com', '$2y$10$wH8K3L8QxK3P7mM5PzQfUu7W2YjM4s6sA7yQm7yG0Y8nT9zL1bR4y', 'admin', '2026-04-09 08:34:32'),
(2, 'leny', 'leny@gg.com', '$2y$10$U.iUArM/qbYv1UH/yHUkPeNANr8ttcjnfvuqfe6M8.iEt0j9Kmokq', 'admin', '2026-04-09 08:35:49'),
(3, 'Enzo', 'boure@gmail.com', '$2y$10$s27iQ7PCVrhNZ7AKEdvrNOb/36276m0PPS0veTElKr/T/rI12XAeW', 'user', '2026-04-27 13:06:20');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_logs_user` (`user_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
