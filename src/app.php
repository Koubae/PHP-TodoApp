<?php declare(strict_types=1);
echo "HCAFD";
exit;
namespace App;
require_once LIB_PATH . "/HTTP/template_engine.php";  // fixme: shouldn't this be loaded by the psr-4 composer autoloder?!?!?!
require realpath(dirname(__DIR__)) . '/src/App/Lib/HTTP/Router.php';

use App\Lib\HTTP\{Router};

// Set Headers
header('Content-Type: text/html; charset=utf-8');

/* @var Lib\App $app Application */
/// Register App Router instance
$router = Router::getClass();
$app->router = $router;
/// Register database cursor to the router for easier faster access
Router::$cr = $app->cr;
Router::$db = $app->database;
Router::authSet($app->auth());

$path = $app->config->getPublic('resources') . '/routes/routes.php';
require $path;


