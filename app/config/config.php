<?php

/*
 * Modified: prepend directory path of current file, because of this file own different ENV under between Apache and command line.
 * NOTE: please remove this comment.
 */
defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');

return new \Phalcon\Config\Config([
    'database' => [
        'adapter'     => match (getenv('DB_CONNECTION')) {
            'mysql', 'mariadb' => 'Mysql',
            'pgsql', 'postgres', 'postgresql' => 'Postgresql',
            'sqlite' => 'Sqlite',
            default => 'Postgresql',
        },
        'host'        => getenv('DB_HOST') ?: 'postgres',
        'port'        => getenv('DB_PORT') ?: '5432',
        'username'    => getenv('DB_USERNAME') ?: 'phalcon_user',
        'password'    => getenv('DB_PASSWORD') ?: 'secret',
        'dbname'      => getenv('DB_DATABASE') ?: 'phalcon_db',
    ],
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'migrationsDir'  => APP_PATH . '/migrations/',
        'viewsDir'       => APP_PATH . '/views/',
        'pluginsDir'     => APP_PATH . '/plugins/',
        'libraryDir'     => APP_PATH . '/library/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'baseUri'        => getenv('APP_BASE_URI') ?: '/',
    ]
]);
