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

        echo "HELLOOOO <br/>";
        echo var_dump($confConnection);
        exit;

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

    }
    private function _create_db(string $db_dump_file): void
    {
        $query = file_get_contents(__DIR__ . "/$db_dump_file");
        $stmt = $this->cr->prepare($query);
        $stmt->execute();
        $stmt->closeCursor();// Safely consuming the SQL operation till end
        if (!$stmt->rowCount()) {
            // Make test query
            $query = 'SELECT id, name, state from project';
            $stmt = $this->cr->prepare($query);
            $stmt->execute();
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