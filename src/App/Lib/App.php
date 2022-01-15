<?php declare(strict_types=1);

namespace App\Lib;
require realpath(dirname(__DIR__)) . '/Lib/DB/Database.php';
use App\Lib\DB\Database;
//use const Config\DEVELOPMENT;
class App
{
    public $config;
    public $development;
    public $database;
    public $cr;
    private $auth;
    public $logger;
    public $router;
    public $appInstance;

    public function __construct($appConfig, $logger)
    {
        $this->config = $appConfig;
        $this->logger = $logger;
        $this->development = \Config\DEVELOPMENT;
    }

    public function run()
    {
        $this->_boot();
    }

    private function _boot()
    {
        $this->logger::enableSystemLogs(request_log: true);
        $this->_bootDatabase();
        $this->auth = new \Delight\Auth\Auth($this->cr);

    }

    private function _bootDatabase()
    {
        $databaseConfig = $this->config::getPrivate("db");
        $database = Database::connect($this->development, $databaseConfig);
        $cr = $database->getCursor();
        $this->database = $database;
        $this->cr = $cr;
    }

    public function auth()
    {
        return $this->auth;
    }




}
