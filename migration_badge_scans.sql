-- ============================================================
-- Migration : Ajout traçage badges RFID
-- Base      : gestion_casiers
-- Date      : 2026-05-27
-- ============================================================

-- 1. Ajout de la colonne badge_uid dans users
--    (si la colonne existe déjà, cette requête retournera une erreur que tu peux ignorer)
ALTER TABLE `users`
  ADD COLUMN `badge_uid` VARCHAR(100) DEFAULT NULL UNIQUE AFTER `role`;

-- 2. Ajout du rôle technicien dans l'ENUM users.role
ALTER TABLE `users`
  MODIFY COLUMN `role` ENUM('admin', 'user', 'technicien') NOT NULL DEFAULT 'user';

-- 3. Création de la table badge_scans
CREATE TABLE IF NOT EXISTS `badge_scans` (
  `id`         INT(11)                              NOT NULL AUTO_INCREMENT,
  `badge_uid`  VARCHAR(100)                         NOT NULL,
  `user_id`    INT(11)                              DEFAULT NULL,
  `user_name`  VARCHAR(100)                         NOT NULL,
  `role`       ENUM('admin', 'user', 'technicien')  NOT NULL,
  `scanned_at` TIMESTAMP                            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_badge_scans_user` (`user_id`),
  CONSTRAINT `fk_badge_scans_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
