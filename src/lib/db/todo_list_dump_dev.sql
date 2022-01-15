/*
*********************************************************************
Sample SQL Dump file
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`my_db` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `my_db`;

--  Destroy any previously created tables in order to start with a fresh database
DROP TABLE IF EXISTS `project`;
DROP TABLE IF EXISTS `task`;
DROP TABLE IF EXISTS `todo_list`;

CREATE TABLE IF NOT EXISTS `project` (
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

    user_id INT UNSIGNED,
    FOREIGN KEY (user_id) REFERENCES `users`(id) ON DELETE CASCADE,

    UNIQUE (name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `task` (
    id MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    datetime_create TIMESTAMP NOT NULL DEFAULT (UTC_TIMESTAMP),
    datetime_write TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    name VARCHAR(50) NOT NULL,
    project_id MEDIUMINT UNSIGNED NULL,
    project_default_id MEDIUMINT UNSIGNED NOT NULL ,

    description TEXT NULL,
    date_start DATE,
    date_end DATE,
    is_done BOOL,
    state ENUM('draft', 'in_progress', 'done', 'discarded') COLLATE utf8_unicode_ci DEFAULT NULL,
    sequence TINYINT UNSIGNED NOT NULL DEFAULT 10,

    UNIQUE unique_project_id_name (project_id, name),
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

    UNIQUE unique_task_id_name (task_id, name),
    FOREIGN KEY (task_id) REFERENCES `task`(id) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_unicode_ci;


CREATE TABLE IF NOT EXISTS `users` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `email` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL,
   `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
   `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
     `email` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL,
     `selector` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
     `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
     `expires` int(10) unsigned NOT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `selector` (`selector`),
     KEY `email_expires` (`email`,`expires`),
     KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- delight-im/PHP-Auth https://github.com/delight-im/PHP-Auth/blob/master/Database/MySQL.sql
--  ================= < USER TABLES > =================
CREATE TABLE IF NOT EXISTS `users_remembered` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user` int(10) unsigned NOT NULL,
    `selector` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `expires` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_resets` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user` int(10) unsigned NOT NULL,
    `selector` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `expires` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `user_expires` (`user`,`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_throttling` (
    `bucket` varchar(44) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `tokens` float unsigned NOT NULL,
    `replenished_at` int(10) unsigned NOT NULL,
    `expires_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`bucket`),
    KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;CREATE TABLE IF NOT EXISTS `users` (
   `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
   `email` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL,
   `password` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
   `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
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
     `email` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL,
     `selector` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
     `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
     `expires` int(10) unsigned NOT NULL,
     PRIMARY KEY (`id`),
     UNIQUE KEY `selector` (`selector`),
     KEY `email_expires` (`email`,`expires`),
     KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- delight-im/PHP-Auth https://github.com/delight-im/PHP-Auth/blob/master/Database/MySQL.sql
--  ================= < USER TABLES > =================
CREATE TABLE IF NOT EXISTS `users_remembered` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user` int(10) unsigned NOT NULL,
    `selector` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `expires` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_resets` (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `user` int(10) unsigned NOT NULL,
    `selector` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `expires` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `selector` (`selector`),
    KEY `user_expires` (`user`,`expires`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_throttling` (
    `bucket` varchar(44) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
    `tokens` float unsigned NOT NULL,
    `replenished_at` int(10) unsigned NOT NULL,
    `expires_at` int(10) unsigned NOT NULL,
    PRIMARY KEY (`bucket`),
    KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===================== < ADD DATA> ===================== --
-- Clean up / Make sure tables are empty
TRUNCATE TABLE users;
TRUNCATE TABLE users_confirmations;
TRUNCATE TABLE users_remembered;
TRUNCATE TABLE users_resets;
TRUNCATE TABLE users_throttling;

TRUNCATE TABLE project;
TRUNCATE TABLE task;
TRUNCATE TABLE todo_list;

INSERT INTO `users` (`id`, `email`, `password`, `username`, `status`, `verified`, `resettable`, `roles_mask`, `registered`)


-- insert data
INSERT INTO `project` (`id`, `name`, `description`, `state`, `is_system_project`, `sequence`, `user_id`) VALUES (1, 'All Project', 'All Project',  'in_progress', true, 1, 1);
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (2, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'et', 'Temporibus accusamus doloremque illum est. Iusto et deserunt et odio occaecati ea voluptas aut. Cumque explicabo officiis et exercitationem minima. Quis et et distinctio tenetur qui.', '1979-08-21', '1989-04-06', 'discarded');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (3, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'similique', 'Quia ut exercitationem omnis animi non delectus. Deserunt doloremque odit voluptatibus ut. Dicta voluptatem dolor nihil aliquid minus molestias nisi. Harum eum sed laborum nihil labore ratione tenetur. Dolor qui praesentium perspiciatis et praesentium et temporibus.', '1975-03-06', '1992-03-28', 'done');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (4, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'voluptatibus', 'Unde atque quam laudantium minus. Consequatur eaque modi quidem et odit consequatur. Quasi sed nam molestias rerum unde excepturi ut.', '1971-10-29', '1975-12-21', 'in_progress');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (5, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'illo', 'Amet est qui nulla numquam animi quaerat. Asperiores exercitationem quasi consequatur et suscipit delectus quis. Et hic maiores consequatur eos dolores quo unde ipsa. Aut fugiat facere laboriosam eius et.', '2016-09-13', '2019-04-26', 'done');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (6, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'facere', 'Est non eaque reiciendis modi odio enim ullam. Eveniet esse dicta libero rerum. Rerum rerum quaerat et eum dicta nesciunt ut. Omnis dolores consequatur tempora et quos deserunt vitae.', '2014-09-22', '1990-01-16', 'in_progress');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (7, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'ex', 'Ut inventore nisi a sit culpa fugiat fugiat soluta. Quas quasi possimus veritatis eum. Commodi asperiores eius minus occaecati aut quibusdam consequuntur sit. Quos earum ipsum reprehenderit fuga rerum rem.', '1996-07-11', '2005-10-02', 'draft');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (8, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'laborum', 'Cupiditate ex tempore quia. Labore et aut modi et. Ab sapiente dolorem quos omnis. Ipsa ipsum veniam ipsum vel quia dignissimos dolor.', '1991-11-01', '1973-08-06', 'discarded');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (9, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'eligendi', 'Ullam quia repellat ad hic qui. Qui alias perspiciatis consequuntur deserunt sed. Et est aut et quod sint dolor.', '2009-11-02', '2005-08-22', 'draft');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (10, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'sint', 'Quod molestiae qui ducimus est. Sint ipsam omnis sit quia quisquam et sed quae. Occaecati dolores omnis suscipit vitae.', '2001-07-06', '2016-10-11', 'draft');
INSERT INTO `project` (`id`, `datetime_create`, `datetime_write`, `name`, `description`, `date_start`, `date_end`, `state`) VALUES (11, '2022-01-04 06:01:47', '2022-01-04 06:01:47', 'reiciendis', 'Est quidem sunt tempora. Dignissimos fuga dolores necessitatibus quia cum. Laudantium sint nesciunt voluptatem nihil eos omnis.', '1976-03-27', '1976-09-17', 'draft');


INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (1, '2000-08-19 15:56:15', '2013-05-19 03:51:38', 'ut', 2, 'Qui fuga pariatur sed quasi omnis autem. At autem sit deserunt ut eos. Impedit ut quia rerum nobis explicabo labore culpa. Recusandae aut sint nihil.', '2020-06-29', '1976-01-28', 1, 'in_progress');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (2, '2000-07-25 17:00:46', '1991-05-30 20:28:35', 'nobis', 2, 'Cupiditate doloremque deleniti ipsum tempore. Numquam est enim facilis minima eum. Eveniet maxime sequi et et.', '2003-11-22', '2004-12-21', 0, 'draft');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (3, '1980-09-26 01:20:03', '1998-04-16 17:46:55', 'illum', 3, 'Aliquid modi non occaecati. Blanditiis qui ut corporis ut. Fugit sit harum et repellat. Soluta dolore incidunt perspiciatis omnis ut quia dolorum. Asperiores exercitationem voluptates occaecati perspiciatis.', '1995-05-02', '2002-08-23', 1, 'in_progress');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (4, '1987-07-16 12:13:11', '1977-04-07 01:19:46', 'fugiat', 4, 'Et aut vel libero et. Vitae asperiores qui rerum ad. Vero nulla eligendi omnis vel aut.', '1974-01-22', '2004-10-08', 0, 'discarded');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (5, '2014-05-09 02:41:59', '2005-03-16 12:21:13', 'et', 5, 'Tempore amet quia accusantium. Enim et atque rem facilis culpa. Ex debitis quod asperiores. Ea qui doloribus placeat pariatur.', '1999-05-29', '1982-07-26', 1, 'draft');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (6, '1986-06-01 11:58:07', '1977-09-01 14:20:50', 'commodi', 6, 'Iure excepturi rerum doloremque tempore reprehenderit. Ipsum culpa fuga adipisci eum magni sed quae sit. Neque placeat maiores occaecati amet voluptatibus ut. Quia similique quia praesentium.', '1998-02-04', '1984-06-03', 1, 'discarded');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (7, '1972-04-01 21:36:00', '2004-01-13 00:31:51', 'rerum', 7, 'Error excepturi placeat doloribus non rerum. Doloribus ratione quisquam consectetur ea molestiae. Est deleniti officiis non sequi doloribus.', '2012-03-09', '2007-10-06', 1, 'discarded');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (8, '2004-01-10 23:00:41', '2007-04-24 08:15:22', 'reprehenderit', 8, 'Debitis deserunt labore omnis non hic tempora voluptatibus. Praesentium aliquam consequatur tempora debitis. Atque eum fuga quo optio aut velit consequatur. Dicta voluptatem dolor ab rerum eum et.', '1986-09-03', '2000-10-26', 0, 'done');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (9, '1997-12-30 17:19:53', '1984-01-14 23:41:34', 'accusantium', 9, 'Tempore sit cupiditate eos sunt. Placeat quam facilis neque porro tempora. Ipsum assumenda aut dolores voluptatibus omnis est cupiditate.', '2018-06-05', '2007-01-17', 0, 'draft');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (10, '1982-11-06 20:54:54', '1988-04-26 15:53:49', 'officia', 10, 'Laborum porro ea distinctio libero enim. Enim omnis placeat blanditiis vitae. Assumenda nisi exercitationem hic autem aut ipsum. Fugit iure ratione voluptas hic doloremque deleniti necessitatibus dolores. Labore eaque ut in aut rem.', '1990-07-19', '1970-04-05', 1, 'in_progress');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (11, '2007-03-14 15:46:20', '1970-12-15 21:58:35', 'sed', 2, 'Similique nulla qui deserunt. Aut sit et magnam qui ullam placeat qui. Molestiae veritatis mollitia aut doloribus.', '2011-09-12', '2001-02-09', 1, 'draft');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (12, '1977-02-18 07:34:08', '1984-09-27 10:42:07', 'qui', 2, 'Aut eius accusantium unde et similique soluta eligendi. Neque sit ducimus voluptatem et. Aut molestias nobis ratione possimus minus.', '1979-11-20', '2004-01-14', 1, 'in_progress');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (13, '2009-06-03 02:31:58', '2012-01-21 13:30:05', 'necessitatibus', 3, 'Qui qui et eum error corporis. Laboriosam consequatur quo laborum rerum.', '2021-07-06', '1998-09-29', 1, 'in_progress');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (14, '2018-04-01 10:53:00', '1996-04-06 21:04:13', 'nisi', 4, 'Dolorum odit voluptates vel totam quisquam. Ea accusamus illo aliquam et voluptatem illo ipsa. Possimus exercitationem fugiat placeat nesciunt placeat et ea.', '1989-03-08', '2010-04-20', 0, 'discarded');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (15, '1993-01-22 15:09:34', '2004-02-27 08:24:22', 'recusandae', 5, 'Et quo ullam qui dolorem illo voluptate. Aut iusto sed dolorem ex id quae. Voluptas consequatur eveniet molestiae quaerat earum.', '2016-05-13', '1984-07-07', 0, 'in_progress');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (16, '2006-04-30 21:49:26', '1988-11-09 23:26:13', 'et', 6, 'Repellendus dolor occaecati praesentium mollitia. Perferendis optio doloremque repudiandae et sed delectus. Placeat quisquam nesciunt cumque reiciendis et ut. Aut aliquid voluptates totam laboriosam.', '1990-10-25', '1971-02-02', 0, 'in_progress');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (17, '2012-12-12 11:04:50', '1971-11-09 17:31:42', 'beatae', 7, 'Doloremque quia dolore eveniet ut mollitia vel labore explicabo. Nulla possimus explicabo deleniti aut omnis consequatur doloribus. Et atque quas quo veritatis quam sunt aliquid. Et et quia quos voluptas. Cupiditate pariatur voluptatem sit.', '1976-06-26', '2009-08-23', 1, 'discarded');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (18, '2014-12-17 22:27:28', '2004-02-21 03:33:07', 'quaerat', 8, 'Voluptas ad deserunt occaecati qui maiores autem nisi aut. Sint enim alias et aperiam debitis sunt. Rem corrupti nulla qui omnis sit sit. Aliquid incidunt ullam nobis sapiente fuga.', '2002-11-28', '2012-03-19', 1, 'draft');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (19, '1978-09-06 13:34:21', '2007-02-05 05:02:47', 'vel', 9, 'Suscipit et quia sequi amet facere. Perspiciatis ab ullam impedit assumenda dicta nam. Corrupti culpa est nesciunt ipsum. Quam dolor consequatur impedit sit eligendi.', '1988-06-04', '1984-07-22', 1, 'discarded');
INSERT INTO `task` (`id`, `datetime_create`, `datetime_write`, `name`, `project_id`, `description`, `date_start`, `date_end`, `is_done`, `state`) VALUES (20, '1991-06-28 11:46:26', '2009-07-20 02:48:22', 'consectetur', 10, 'Quas ea ratione facilis et. Distinctio a quam nostrum repudiandae in quibusdam ad. Quod autem et voluptas incidunt cupiditate. Aut et dolorem eum ullam ipsa culpa esse alias.', '1987-08-01', '1998-06-08', 0, 'draft');

INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (51, '1999-06-03 12:31:04', '2005-07-17 12:52:39', 'quia', 1, 'Ipsa numquam itaque voluptas aperiam quia. Accusantium aut voluptatem rerum provident incidunt. Et aperiam exercitationem ipsam unde.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (52, '2010-12-02 16:22:57', '2003-07-04 01:57:43', 'voluptates', 2, 'Dolores eius sunt quia non quia ullam quo. Quia delectus qui illum vitae sunt dolores. Quo deserunt porro et et earum hic consequatur sunt.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (53, '1999-04-22 11:31:33', '1980-12-22 11:51:58', 'consequatur', 3, 'Molestias delectus quaerat veritatis animi ratione minima. Quisquam eos aliquam laboriosam quibusdam nobis corporis ut doloribus. Impedit cumque sint suscipit et temporibus.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (54, '2015-12-02 06:15:21', '1986-08-31 20:44:04', 'est', 4, 'Dignissimos nihil tempore fugiat qui dolorem deleniti rerum. Dolorem similique et molestiae facere. Hic reprehenderit voluptatem iusto. Quo suscipit sit sed quia dolorum vel vitae aut.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (55, '2007-11-29 20:12:48', '1988-06-11 19:53:44', 'nihil', 5, 'Quod ad ipsum dolor eos eligendi reprehenderit dolor. Id perferendis facilis sed possimus. Repellendus quis nobis soluta eos nesciunt. Laborum ut et dolorem dolore quidem. Tempore minus non error placeat velit omnis aspernatur.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (56, '1986-10-17 22:26:42', '1975-09-16 12:37:02', 'voluptatum', 6, 'Voluptates voluptas commodi molestiae harum. Rem et pariatur ut aliquid totam. Quisquam sed dolore expedita et asperiores sed.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (57, '2011-04-14 14:04:51', '2012-07-01 17:47:38', 'numquam', 7, 'Et ut eligendi reiciendis quia. Eveniet excepturi aspernatur voluptatum perspiciatis odit non. Facilis error blanditiis quos ut molestiae facilis vel. Aut qui similique hic illum. Aut in nihil ducimus minus.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (58, '1997-10-06 22:32:59', '2013-04-03 16:01:01', 'blanditiis', 8, 'Iusto officiis explicabo in rem voluptatem beatae. Expedita qui cum ut rerum rerum quia omnis. Porro est quia mollitia et.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (59, '1972-06-30 19:51:21', '1977-12-20 05:52:47', 'quisquam', 9, 'Recusandae laborum cum fugit quaerat. Ducimus et illo non. Corporis unde saepe est quas assumenda vel. Delectus expedita ea omnis sed exercitationem.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (60, '1986-08-05 15:52:15', '1991-12-16 17:20:42', 'qui', 10, 'Cum sed ex numquam aut. Nesciunt voluptas dicta ut labore accusantium vitae. Tenetur quisquam at sint aut et aut. Voluptates impedit vitae molestiae. Optio esse voluptatum autem ut quod vero.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (61, '2004-09-29 06:24:39', '1979-04-18 10:46:51', 'labore', 11, 'Sint porro et rerum blanditiis est. Rerum voluptas ut quo ab corporis et. Quis voluptas eligendi sequi dicta.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (62, '1983-07-27 19:58:38', '1989-09-19 04:13:37', 'voluptatibus', 12, 'Voluptas odio voluptates sunt et. Voluptatem est nihil esse asperiores in sit excepturi. Dicta eos inventore aspernatur minima qui nihil.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (63, '2013-08-09 23:59:02', '2003-07-28 15:43:48', 'odit', 13, 'Praesentium error itaque nihil facere eos. Quod et dolor delectus amet. Aut eius ducimus magni ea ut.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (64, '1996-12-04 02:08:54', '1997-09-04 13:56:00', 'ut', 14, 'Culpa autem dolorem cupiditate qui. Ullam et omnis laboriosam ut repellendus dolore sed. Saepe et quia eum consequuntur reprehenderit cumque. Modi dicta fugiat esse ut vel.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (65, '2010-09-22 11:14:10', '1980-08-06 01:35:17', 'numquam', 15, 'Quia vel minima animi et deleniti atque consequatur. Commodi maxime asperiores dolorum odio numquam at similique. Et eveniet consequatur vel. Maiores laborum tempora illum. Aut cupiditate qui aliquid mollitia expedita explicabo.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (66, '1977-03-08 01:08:33', '1984-01-10 07:15:37', 'adipisci', 16, 'Est accusamus quo facilis ullam cum non et. Commodi quidem corrupti et laudantium. Molestiae quam nam facere.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (67, '2012-04-21 22:55:06', '2019-08-25 15:59:48', 'perferendis', 17, 'Reprehenderit illo accusamus ut animi. Possimus inventore distinctio et. Sed voluptas iure repellat dolorem.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (68, '2008-02-03 23:52:46', '2005-06-01 20:32:57', 'inventore', 18, 'Amet enim sit dolorem ut. Reprehenderit nisi inventore nihil alias dolor laudantium sapiente. Quo minima expedita atque quisquam id est dolores nihil. Amet repellendus corporis aut quo officiis officiis.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (69, '2005-12-08 19:54:55', '1978-04-23 10:45:10', 'ea', 19, 'Iusto placeat at non sit. Quidem libero fugit reiciendis accusamus. Deserunt enim consequatur eaque fuga et.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (70, '1970-01-25 16:57:57', '1977-12-25 16:10:27', 'est', 20, 'Dignissimos veniam praesentium nostrum id vero ab. Eaque consequuntur aut nam placeat. Sint maxime tenetur ut.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (71, '1990-10-14 15:05:53', '1982-04-26 09:49:28', 'aut', 1, 'Repellendus autem ex deleniti sed quia. Quas error amet praesentium necessitatibus. Sit et optio consequatur ipsum consequuntur accusantium. Voluptatem amet voluptatem possimus est velit quos tempora. Voluptas voluptatum et totam enim.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (72, '1997-09-16 05:41:41', '2003-10-23 19:46:56', 'qui', 2, 'Laudantium veniam voluptate facilis odio labore est quidem. Quisquam voluptatem et id aut laborum eum ut. Esse sit aut ut molestiae aut repellat neque.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (73, '1971-06-23 05:39:23', '1994-11-16 20:39:10', 'cumque', 3, 'Et autem quidem minus. Sunt fuga repellat unde. Illo repellendus sequi optio ut pariatur. Illo et rem et occaecati incidunt doloribus.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (74, '1970-04-29 02:43:39', '2007-01-12 09:13:01', 'ea', 4, 'Quasi voluptas et est quod et aliquam. Consectetur odit cum dolores at incidunt nisi dolorum excepturi. Deleniti deleniti eius placeat aperiam animi.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (75, '2016-10-06 09:29:24', '1978-11-24 04:10:15', 'fuga', 5, 'Dolorem nihil ratione accusantium. Autem non ex autem qui vel dolor qui. Nostrum laboriosam et aut dolorem. Quis laudantium laudantium quos nemo consequatur suscipit.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (76, '2002-12-02 00:36:50', '2005-02-14 06:43:15', 'odio', 6, 'Ut et magnam consectetur dolorum. Autem possimus ea magni neque modi incidunt dolores. Quam illum modi quo similique qui qui omnis accusamus. Debitis qui dolore in modi aliquid.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (77, '1980-11-22 20:56:37', '1989-04-15 06:39:28', 'odio', 7, 'Culpa ex eum voluptatem minima omnis impedit. Aut harum consectetur ab accusantium. Velit placeat in doloremque sit in. Labore provident corrupti illum voluptates.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (78, '1986-12-11 17:48:22', '1986-02-15 05:57:37', 'praesentium', 8, 'Est maxime quia accusantium officia nesciunt. Earum vel aliquam quia itaque quaerat optio.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (79, '2018-07-16 05:38:07', '1988-12-16 12:32:41', 'consequatur', 9, 'Possimus sed impedit totam. Sed quibusdam aperiam culpa molestias harum illo qui. Similique id iure nam facilis.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (80, '2005-03-13 16:14:32', '1976-05-30 22:12:15', 'velit', 10, 'Adipisci earum mollitia porro incidunt. Ipsam aut maiores amet quae et. Error aliquid autem velit eos. Quia quia non doloremque eligendi sed ut. Quia consequatur aut porro blanditiis itaque laudantium doloribus.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (81, '2003-06-02 05:53:28', '1974-03-09 13:03:17', 'natus', 11, 'Autem tempora est ea qui voluptatum expedita assumenda perferendis. Laudantium ullam est consequuntur. Sed qui sunt tempore.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (82, '2010-10-09 08:35:48', '2002-01-21 19:49:42', 'hic', 12, 'Nostrum enim aut est hic et. Expedita dolorem minus aut totam voluptates nisi. Quis tenetur autem labore et alias ullam velit.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (83, '1985-03-13 13:02:01', '1975-06-28 03:50:51', 'nam', 13, 'Et odit voluptas temporibus. Quasi consequatur sit aut laborum. Qui et odit optio possimus aut cum itaque.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (84, '1997-11-26 20:32:40', '2009-09-28 00:58:46', 'dolorem', 14, 'Animi consectetur sapiente doloribus iste. Et accusantium necessitatibus consectetur. Laboriosam at in qui.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (85, '1996-11-06 12:46:23', '1986-12-27 21:05:14', 'rerum', 15, 'Voluptate sequi dolorem sed. Qui omnis facilis eveniet ea et expedita at. Hic nihil iusto veritatis hic rerum in. Voluptatibus consectetur sit culpa architecto culpa.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (86, '1980-05-01 03:49:58', '2005-08-06 04:35:36', 'maxime', 16, 'Possimus totam qui suscipit et est eaque officiis sit. Doloribus amet necessitatibus est. Laudantium ea atque quae.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (87, '1975-12-05 03:10:27', '2004-11-18 14:43:02', 'nulla', 17, 'Voluptatem suscipit provident illo recusandae. Et consequatur aspernatur doloribus hic. Voluptatum et eligendi optio accusantium expedita in sapiente neque.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (88, '2019-09-14 23:43:35', '1975-08-13 17:55:51', 'exercitationem', 18, 'Est sunt eveniet velit vitae. Aperiam odio debitis est explicabo ad.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (89, '2013-05-07 05:04:14', '2014-05-04 10:42:23', 'laudantium', 19, 'Est alias eos ut consequatur reprehenderit. Fuga alias iusto quia non nihil impedit ducimus earum. Expedita ea distinctio fugiat distinctio assumenda.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (90, '2017-05-21 21:18:06', '2002-08-29 10:41:40', 'et', 20, 'Consequatur provident eveniet voluptates qui esse. Tempora dolore et sequi. Nihil consequatur et voluptas fuga.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (91, '1976-07-14 06:57:49', '2005-04-27 12:18:22', 'dolores', 1, 'Laboriosam a dolor dicta est. Voluptatum repellat magnam quo nisi fugit. Pariatur qui omnis sed illo enim.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (92, '1983-03-02 22:29:01', '2015-02-12 19:04:53', 'tempore', 2, 'Voluptates pariatur praesentium doloremque earum. Eum non qui totam ut. At nisi nobis qui ab eius sint.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (93, '1971-11-20 17:53:19', '1987-02-19 04:15:47', 'dolores', 3, 'Minima adipisci molestias tempora nemo accusamus placeat esse. Id et ex et magni doloremque. Veritatis maxime voluptas quidem itaque.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (94, '2016-09-26 08:43:45', '1978-12-22 13:45:33', 'quibusdam', 4, 'Dolorem culpa quam inventore aut modi. Nostrum nulla tempora ullam labore quidem. Placeat aut magnam nostrum maxime. Laboriosam dolores sit repellat aut et. Expedita modi consequatur ipsa qui ut adipisci unde.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (96, '2005-12-06 10:00:37', '1992-01-15 10:05:23', 'ut', 6, 'Labore ut quo qui accusantium. Enim est ea repudiandae aspernatur. Aut possimus libero eligendi a mollitia veritatis. Sint hic pariatur eligendi.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (97, '2015-04-19 15:35:38', '1985-01-10 08:08:13', 'unde', 7, 'Et nihil et voluptate fugiat. Reprehenderit non eum reprehenderit. Sed necessitatibus ipsa eum cumque incidunt reprehenderit. Voluptatibus ea quos sint eligendi. In aperiam beatae ipsam explicabo illum.', 1);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (98, '1996-02-29 06:33:36', '1987-01-24 23:42:08', 'dolor', 8, 'Qui ut voluptatum consequatur et aliquid dignissimos. Explicabo vitae amet alias qui voluptate error blanditiis. Nihil cupiditate aperiam non error.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (99, '1998-08-15 23:15:45', '1970-08-21 14:15:27', 'molestiae', 9, 'Alias quaerat rerum architecto saepe cumque non. Laboriosam nisi libero unde facere.', 0);
INSERT INTO `todo_list` (`id`, `datetime_create`, `datetime_write`, `name`, `task_id`, `description`, `is_done`) VALUES (100, '2013-07-15 18:22:00', '1995-09-21 05:58:33', 'laboriosam', 10, 'Atque et eius ea quis qui est adipisci. Cumque tenetur sint recusandae doloribus vel explicabo dolore.', 1);





/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
