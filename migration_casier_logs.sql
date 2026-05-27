-- ============================================================
-- Migration : Historique des ouvertures de casiers
-- Base      : gestion_casiers
-- Date      : 2026-05-27
-- ============================================================

CREATE TABLE IF NOT EXISTS `casier_logs` (
  `id`         INT(11)       NOT NULL AUTO_INCREMENT,
  `user_id`    INT(11)       DEFAULT NULL,
  `user_name`  VARCHAR(100)  NOT NULL,
  `badge_uid`  VARCHAR(100)  DEFAULT NULL,
  `site`       ENUM('A','B') NOT NULL,
  `casier`     TINYINT(1)    NOT NULL,
  `opened_at`  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_casier_logs_user` (`user_id`),
  CONSTRAINT `fk_casier_logs_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
