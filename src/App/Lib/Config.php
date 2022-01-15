<?php declare(strict_types = 1);

namespace App\Lib;

require_once __DIR__ . '../../../app_config.php';
use const App\BASE_DIR;
require_once BASE_DIR . '/config/config.php';
use const Config\CONFIG;
echo var_dump(CONFIG);
echo var_dump(CONFIG);
echo var_dump(CONFIG);
final class Config
{

    private static self $instance;
    /*** private settings holds db credentials and anything that the user should not see
    ** @var array $private
     */
    private static array $private = [];

    /**
     * @var array $public Public settings anything that is served to the user
     */
    private static array $public = [];

    /**
    ----------------------------
    CONSTRUCTOR
    ----------------------------
     */
    public static function getClass(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /// Disable Class Instantiation
    private function __construct() {}
    /// Disable Class Coping (Not actual needed since __construct() is also private but adds clarity
    private function __clone() {}

    /**
        ----------------------------
            GETTERS
        ----------------------------
    */

    public static function getPrivate(string|int $key): mixed
    {
        return self::$private[$key] ?? false;
    }

    public static function getPublic(string|int $key): mixed
    {
        return self::$public[$key] ?? false;
    }

    /**
        ----------------------------
        SETTERS
        ----------------------------
     */
    /** Configure the app with the config file
     * @var array Config\CONFIG Multidimensional Array, keys at level 1 are different settings parent, each containing
     * 2 array with private & public array configuration.
     * @return void
     * @throws \Exception  If wrong parameter is passed
     */
    public static function initConfig(): void
    {
        foreach(CONFIG as $config => $setting) {
            // Config Is a multi-dimenstion array containing each 'private' & 'public' arrays of settings
            foreach($setting as $permission => $values) {
                if ($permission === 'private') {
                    foreach($values as $key => $value) {
                        self::setPrivate($key, $value);
                    }
                } elseif ($permission === 'public') {
                    foreach($values as $key => $value) {
                        self::setPublic($key, $value);
                    }
                } else {
                    throw new \Exception('Wrong Config parameter, expected private / public but got --> ' . $key);
                }
            }
        }
    }

    public static function setPrivate(string|int $key, mixed $value):bool
    {
        self::$private[$key] = $value;
        return true;
    }
    public static function setPublic(string|int $key, mixed $value):bool
    {
        self::$public[$key] = $value;
        return true;
    }


    public function __get(string|int $key): mixed
    {
        return self::$public[$key] ?? false;
    }
    public function __isset(string|int $key):bool
    {
        return isset(self::$public[$key]);
    }


}

