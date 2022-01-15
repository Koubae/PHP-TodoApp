<?php declare(strict_types = 1);

require realpath(dirname(__DIR__)) . '/vendor/autoload.php';
require realpath(dirname(__DIR__)) . '/src/App/Lib/Config.php';
require realpath(dirname(__DIR__)) . '/src/App/Lib/Logger.php';
require realpath(dirname(__DIR__)) . '/src/App/Lib/App.php';
use App\Lib\{Config, Logger, App};

$config = Config::getClass(); // pass the class in app and not the instance, learn how.
$config::initConfig();

$logs = $config::getPrivate('logs');

$logger = Logger::getInstance(key: 'app', log_path:$logs);
$app = new App(appConfig: $config, logger: $logger);

$app->run();
echo "HELLOOOO <br/>";
echo var_dump($app);
exit;

require 'app.php';