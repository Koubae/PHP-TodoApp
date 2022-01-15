<?php declare(strict_types=1);

namespace App\Lib\DB;

final class Database
{
    const CONN_DB_NAME = false;
    private string $db_host;
    private string $db_name;
    private \PDO $cr;

    private function __construct(string $db_host, string $db_name)
    {
        $this->db_host = $db_host;
        $this->db_name = $db_name;
    }

    public static function connect(bool $development,array $databaseConfig) : self
    {

        if ($development) {
            $confConnection = $databaseConfig['development'];
        } else {
            $confConnection = $databaseConfig['production'];
        }
        list(
            'db_host' => $db_host,
            'db_name' => $db_name,
            'db_user' => $db_user,
            'db_pass' => $db_pass,
            'db_dump_file' => $db_dump_file
            ) = $confConnection;



        $conn = new Database($db_host, $db_name);
        if (self::CONN_DB_NAME) {
            $dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8";
        } else {
            $dsn = "mysql:host=" . $db_host . ";charset=utf8";
        }


        $cr = self::_connect($dsn, $db_user, $db_pass);
        if (!$cr) {
            die("Unable to connect to the site Database.");
        }
        $conn->cr = $cr;
        $conn->_init_db($db_dump_file);
        return $conn;
    }

    /**
        ----------------------------
        Connection Methods
        ----------------------------
     */

    private static function _connect($dsn, $db_user, $db_pass): \PDO
    {
        try  {
            $cr = new \PDO($dsn, $db_user, $db_pass);
            $cr->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            return $cr;
        } catch (\PDOException $e) {
            exit;
        }
    }
    private function _init_db($db_dump_file)
    {
        try {
            $this->_testConnection();
        } catch (\PDOException $e) {
            $this->_create_db($db_dump_file);
        } catch (\Exception $e) {
            $this->_create_db($db_dump_file);
        }
    }
    private function _testConnection():void
    {

        $this->cr->query("USE $this->db_name");
        $this->cr->query("SELECT COUNT(*) FROM project LIMIT 1");

    }
    private function _create_db(string $db_dump_file): void
    {
//        $query = file_get_contents(__DIR__ . "/$db_dump_file");

    $query = "    
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
";

        try {
            $this->cr->setAttribute(\PDO::ATTR_EMULATE_PREPARES, 0);
            $this->cr->query("create database IF NOT EXISTS `heroku_cd7e75e609cc863`");
            $this->cr->query("use " . $this->db_name);
            $stmt = $this->cr->prepare($query);
            $stmt->execute();
            echo "dfsfd";
            exit;
        } catch(\PDOException $e ) {
            echo "ERROR $e";
            exit;
        }
    }

    public function getCursor(): \PDO
    {
        return $this->cr;
    }

    public function getDBHost(): string
    {
        return $this->db_host;
    }
    public function getDBName(): string
    {
        return $this->db_name;
    }
}