<?php declare(strict_types=1);

namespace App\Lib;

use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;

class Logger extends \Monolog\Logger
{
    private static $loggers = [];
    private static $log_path;

    private function __construct($key="app", $log_path=null)
    {
        parent::__construct($key);
        if (!isset(self::$log_path)) {
            self::$log_path = $log_path;
        }

        $config = [
            'logFile' => self::$log_path . "/{$key}.log",
            'logLevel' => \Monolog\Logger::DEBUG
        ];

        $this->pushHandler(new StreamHandler($config['logFile'], $config['logLevel']));
    }

    public static function getInstance(string $key, string $log_path) : self
    {
        if (empty(self::$loggers[$key])) {
            self::$loggers[$key] = new Logger($key, $log_path);
        }

        return self::$loggers[$key];
    }

    public static function enableSystemLogs($request_log=false)
    {

        $log_path = self::$log_path;

        // Error Log
        self::$loggers['error'] = new Logger('errors');
        self::$loggers['error']->pushHandler(new StreamHandler("{$log_path}/errors.log"));
        ErrorHandler::register(self::$loggers['error']);

        // Request Log
        if ($request_log) {
            $data = [
                $_SERVER,
                $_REQUEST,
                trim(file_get_contents("php://input"))
            ];
            self::$loggers['request'] = new Logger('request');
            self::$loggers['request']->pushHandler(new StreamHandler("{$log_path}/request.log"));
            self::$loggers['request']->info("REQUEST", $data);
        }

    }

    public function showLoggers() {
        return self::$loggers;
    }
}
