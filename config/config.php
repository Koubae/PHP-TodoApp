<?php declare(strict_types = 1);

namespace Config;

/*
====================
APPLICATION CONSTANTS
====================
*/
const ENV = 'PRODUCTION';
const DEVELOPMENT = ENV === 'DEVELOPMENT';
if (DEVELOPMENT) {
    // Set Errors level
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    ini_set("error_reporting", "true");
    error_reporting(E_ALL);
}
define('MAX_PROJECT', 12);


/*
====================
    CONFIGS
====================
*/
const CONFIG = [

    'main' => [
        'private' => [
            'env' => ENV,
            'dev' => DEVELOPMENT,
        ],
        'public' => [
            'name' => 'TodoApp',
//            'url' => 'localhost',
            'url' => '',

        ],
    ],

    'paths' => [
        'private' => [
            'lib' => APP_DIR . '/lib',
            'logs' => ROOT_DIR . '/logs',
        ],
        'public' => [
            'assets' => '/assets',
            'resources' => ROOT_DIR . '/resources',
            'views' => ROOT_DIR . '/resources/views',
        ],
    ],

    'credentials' => [
        'private' => [
            'db' => [
//                'development' => [
//                    'db_host' => 'localhost',
//                    'db_name' => 'my_db',
//                    'db_user' => 'root',
//                    'db_pass' => '',
//                    'db_dump_file' => 'todo_list_dump_dev.sql',
//                ],
//                'production' => [
//                    'db_host' => 'localhost',
//                    'db_name' => 'todo_list',
//                    'db_user' => 'root',
//                    'db_pass' => '',
//                    'db_dump_file' => 'todo_list_dump.sql',
//                ],
                'production' => [
                    'db_host' => '',
                    'db_name' => '',
                    'db_user' => '',
                    'db_pass' => '',
                    'db_dump_file' => 'todo_list_dump.sql',
                ],

            ]
        ],
        'public' => [],
    ],

];

/*
====================
   APP CONSTANTS Shortcuts
====================
*/
// PUBLIC
//define("WEB_HOST" , 'http://' . CONFIG['main']['public']['url'] . ':8000');
define("WEB_HOST" , 'https://' . CONFIG['main']['public']['url']);
define('ASSETS', CONFIG['paths']['public']['assets']);
define('ASSETS_STYLE', ASSETS . '/style/');
define('ASSETS_JS', ASSETS . '/js/');
define('VIEWS', realpath(dirname(__DIR__)) . '/resources/views');

// PRIVATE
define('LIB_PATH', CONFIG['paths']['private']['lib']);
