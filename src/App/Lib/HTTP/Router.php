<?php declare(strict_types=1);

namespace App\Lib\HTTP;
//use  \App\Lib\DB\Database;
//https://stackoverflow.com/a/11723153/13903942
require realpath(dirname(__DIR__)) . '/HTTP/Request.php';
require realpath(dirname(__DIR__)) . '/HTTP/Response.php';


class Router
{
    public static \PDO $cr;
    public static \App\Lib\DB\Database $db;
    public static array $routes;
    private static $_instance = null;
    private static \Delight\Auth\Auth $auth;
    public static $rootURL = WEB_HOST;

    private function __construct() {}
    private function __clone() {}
    /**
    ----------------------------
    CONSTRUCTOR
    ----------------------------
     */
    public static function getClass(): self
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * @public
     */
    public function registerAppRoutes(array $routes): self
    {
        // TODO: implement register app routes
        return $this;
    }

    /* ================================================
                    AUTH
    /* ================================================
     */
    public static function auth()
    {
        return self::$auth;
    }
    public static function logged()
    {
        if (!self::$auth) return false;
        return self::$auth->isLoggedIn();
    }
    public static function getUserId()
    {
        if (!self::$auth) return null;
        return self::$auth->getUserId();
    }
    public static function getUserEmail()
    {
        if (!self::$auth) return null;
        return self::$auth->getEmail();
    }
    public static function authSet($auth)
    {
        self::$auth = $auth;
        return true;
    }

    /* ================================================
                    HTTP METHODS
    /* ================================================
     */

    /**
     * @public
     */
    public static function get($route, $callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }
        echo var_dump($route); echo  '<br/>';
        echo var_dump($_SERVER['REQUEST_URI']); echo  '<br/>';
        exit;

        return self::_route($route, $callback, "GET");
    }

    public static function post($route, $callback)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') !== 0) {
            return;
        }
        return self::_route($route, $callback, "POST");
    }

    /* ================================================
                    ROUTING
    /* ================================================
     */

    private static function _route($route, $callback, ?string $method='GET')
    {
        try {
            $logged = self::logged();
            $userLogginAttempt = $_SERVER['REQUEST_URI'] === '/login?submit' && $method === 'POST';
            $userSignUpAttempt = $_SERVER['REQUEST_URI'] === '/signup?submit' && $method === 'POST';

            echo var_dump($route); echo  '<br/>';
            echo var_dump($userLogginAttempt); echo  '<br/>';
            echo var_dump($userSignUpAttempt); echo  '<br/>';
            echo var_dump($_SERVER['REQUEST_URI']); echo  '<br/>';
            exit;

            if (!$logged && !$userLogginAttempt && !$userSignUpAttempt) {
                return self::redirectLogin();
            }

            return self::__route($route, $callback);
        } catch( \Exception $e) {
            try {
                $errorCode = (int) $e->getCode();
            } catch (\TypeError) {
                $errorCode = 404;
            }
            return self::errorNotFound(statusCode: $errorCode);
        }
    }
    /**
     * @private
     *
     */
    private static function __route(string|array $route, callable|int $callback): void
    {
        self::_validateCallback($callback);
        [$match, $matches] = self::_findRoute($route);
        if (!$match) {
            // will fallback to the next router handler
            return;
        }
        // remove the first match which is the route and we don't need it
        array_shift($matches);
        //performs some clean up of the regex because we only need the first index of each match: todo: improve
        $reqMatches = array_map(function ($match) {
            return $match[0];
        }, $matches);

        // Should do return call_user_func_array($route['callback'], $matches);
        $callback( new Request($_REQUEST, $reqMatches), new Response());
        exit;

    }

    /**
     *
     * @param callable | int $callback if callable should be the route handler else the status code
     * @return void
     * @throws \Exception
     */
    private static function _validateCallback(callable | int $callback)
    {
        if (!is_callable($callback))  {
            if (is_int($callback)) {
                $errorCode = $callback;
            } else {
                $errorCode = 404; // Falback
            }
            throw new \Exception("Route not found", code: $errorCode);
        }
    }

    /**
    * Finds the Route match
    *
    *
     * @param string $route: Request Client Route
     */
    private static function _findRoute(string | array $route): array
    {
        // DETERMINE WHETHER THE $route is one route or multiple routes
        if (is_array($route)) {
            foreach($route as $path) {
                [$match, $matches] = self::_routeRegexing($path);
                if ($match) break;
            }
        } elseif (is_string($route)) {
            [$match, $matches] = self::_routeRegexing($route);
        } else {
            throw new \Exception("Wrong datatype passed as route, expected type string or array but got " . gettype($route));
        }
        return array($match, $matches);
    }
    private static function _routeRegexing(string $route): array
    {

        $uri = $_SERVER['REQUEST_URI'];
        if (substr($uri, -1) === '?') {
            $uri = substr_replace($uri, "", -1);
        }
        if (substr($uri, -1) === '&') {
            $uri = substr_replace($uri, "", -1);
        }
        $params = stripos($uri, '/') !== 0 ? "/" . $uri : $uri; // Add '/' to beggining of URI if there isnt a '/' at the beginnig
        $regex = str_replace('/', '\/', $route);
        $match = preg_match('/^' . ($regex) . '$/', $params, $matches, PREG_OFFSET_CAPTURE);
        return array($match, $matches);
    }

    /* ================================================
                    Redirecting
    /* ================================================
     */

    public static function errorNotFound(int | string | null $statusCode = 404)
    {
        http_response_code($statusCode);
        return render("errors/404.php", ["error_code" => $statusCode]);
    }

    public static function redirectLogin()
    {
        if ($_SERVER['REQUEST_URI'] !== '/login') echo self::redirect(slug: 'login'); // Makes sure that it redirect to the login URI
        return render("login.php", ["logged" => self::logged()]);
    }

    /***
     * Redirect to the given root/slug url location
     * @param string|null $root  Root url default to the server host domain, example https://google.com
     * @param string $slug      Additional slug url: defaults to '/' example https://google.com/maps
     * @return string   Returns a Javascript script with window.location.href of the target url
     */
    public static function redirect(?string $root = null, string $slug = '/'): string
    {
        if (!$root) {
            $root = self::$rootURL;
        }
        if (!str_starts_with($slug, '/')) {
            $slug = '/' . $slug;
        }
        return <<<REDIRECT
        <script type="text/javascript">
            window.location.href = '$root$slug' ;
        </script>
        REDIRECT;

    }


}