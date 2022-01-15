<?php declare(strict_types=1);

use App\Lib\HTTP\{Router};

/* @var $app Application */
/* @var $router App\Lib\HTTP\Router */
$thisDir = $app->config->getPublic('resources') . '/routes';



require_once $thisDir . "/handlers/handlers.php";
/**
 * @var $index
 * @var $login
 * @var $logout
 * @var $loginSubmit
 * @var $signupSubmit
 * @var $taskCreate
 * @var $projectCreate
 * @var $taskUpdate
 * @var $taskUpdateToggleDone
 * @var $taskDelete
 * @var $projectDelete
 *
 */

// todo: update the Router in order to be able to interprete the routes using this system below
// is slilighy more verbose but i think adds more clarity and is more beautiful.
// furthermore we can properly name the routes, creating a 'resource' system.
// Also, it should be faster and we can implement something similar to FastRoute
// where the regex is a huge unique regex
$routes = [
    'index' => [
        'routes' => ['/', '/home', '/index'],
        'method' => 'GET',
        'handler' => $index,
    ],
    // .... todo...
];

$router->registerAppRoutes($routes);


// ======================
//          CRUD
// ======================
// Home Page
// Read
Router::get([
    '/',
    '/home',
    '/index',
    '/(\?)project=([\d]+)',
    '/home(\?)project=([\d]+)',
    '/index(\?)project=([\d]+)'
],
    $index);

// Login
Router::get('/login', $login);
Router::post('/login(\?)submit', $loginSubmit);
Router::post('/signup(\?)submit', $signupSubmit);
Router::get('/logout', $logout);

// Create
Router::post(['/task'], $taskCreate);
Router::post(['/project'], $projectCreate);
// Update
Router::post(['/task/([\d]+)'], $taskUpdate);
Router::post(['/task/([\d]+)/done'], $taskUpdateToggleDone);

// Delete
Router::post(['/task/([\d]+)/delete'], $taskDelete);
Router::post(['/project/([\d]+)/delete'], $projectDelete);


// Errors
// NOTE : In order to throw a client error just add a status code into the call back para
//// will check if not callable and thwows an error
Router::get('(.*?)', 404);





