USE `heroku_cd7e75e609cc863`;


CREATE TABLE `project` (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    datetime_create TIMESTAMP NOT NULL DEFAULT (UTC_TIMESTAMP),
    datetime_write TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    name VARCHAR(50) NOT NULL,

    description TEXT NULL,
    date_start DATE,
    date_end DATE,
    state ENUM('draft', 'in_progress', 'done', 'discarded')  COLLATE utf8_unicode_ci DEFAULT NULL,
    is_system_project BOOL DEFAULT FALSE,
    sequence TINYINT UNSIGNED NOT NULL DEFAULT 10,

    user_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES `users`(id) ON DELETE CASCADE,


    UNIQUE (name, user_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `task` (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    datetime_create TIMESTAMP NOT NULL DEFAULT (UTC_TIMESTAMP),
    datetime_write TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    name VARCHAR(50) NOT NULL,
    project_id MEDIUMINT UNSIGNED NULL,
    project_default_id MEDIUMINT UNSIGNED NOT NULL DEFAULT 1,

    description TEXT NULL,
    date_start DATE,
    date_end DATE,
    is_done BOOL,
    state ENUM('draft', 'in_progress', 'done', 'discarded') COLLATE utf8_unicode_ci DEFAULT NULL,
    sequence TINYINT UNSIGNED NOT NULL DEFAULT 10,
    user_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES `users`(id) ON DELETE CASCADE,


    UNIQUE unique_user_project_id_name (user_id, project_id, name),
    FOREIGN KEY (project_id) REFERENCES `project`(id) ON DELETE CASCADE,
    FOREIGN KEY (project_default_id) REFERENCES `project`(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `todo_list` (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    datetime_create TIMESTAMP NOT NULL DEFAULT (UTC_TIMESTAMP),
    datetime_write TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    name VARCHAR(50) NOT NULL,
    task_id MEDIUMINT UNSIGNED NOT NULL,

    description TEXT NULL,
    is_done BOOL,
    sequence TINYINT UNSIGNED NOT NULL DEFAULT 10,
    user_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES `users`(id) ON DELETE CASCADE,

    UNIQUE unique_user_id_task_id_name (user_id, task_id, name),
    FOREIGN KEY (task_id) REFERENCES `task`(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


-- delight-im/PHP-Auth https://github.com/delight-im/PHP-Auth/blob/master/Database/MySQL.sql
--  ================= < USER TABLES > =================
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `email` varchar(249)  NOT NULL,
    `password` varchar(255)  NOT NULL,
    `username` varchar(100)  DEFAULT NULL,
    `status` tinyint(2) unsigned NOT NULL DEFAULT '0',
    `verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
    `resettable` tinyint(1) unsigned NOT NULL DEFAULT '1',
    `roles_mask` int(10) unsigned NOT NULL DEFAULT '0',
    `registered` int(10) unsigned NOT NULL,
    `last_login` int(10) unsigned DEFAULT NULL,
    `force_logout` mediumint(7) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_confirmations` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `user_id` int(10) unsigned NOT NULL,
    `email` varchar(249) NOT NULL,
    `selector` varchar(16)NOT NULL,
    `token` varchar(255)  NOT NULL,
    `expires` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `email_expires` (`email`,`expires`),
    KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_remembered` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `selector` varchar(24)  NOT NULL,
  `token` varchar(255)  NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `selector` (`selector`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_resets` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user` int(10) unsigned NOT NULL,
    `selector` varchar(20) NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `user_expires` (`user`,`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_throttling` (
    `bucket` varchar(44) NOT NULL,
    `tokens` float unsigned NOT NULL,
    `replenished_at` int(10) unsigned NOT NULL,
    `expires_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`bucket`),
    KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
