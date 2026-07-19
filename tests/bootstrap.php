<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Phalcon\Di\FactoryDefault;
use Dotenv\Dotenv;

// Define constants
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . '/app');
}

// Load environment variables for testing
$dotenv = Dotenv::createImmutable(BASE_PATH, '.env.testing');
$dotenv->safeLoad();

// Initialize DI for tests
$di = new FactoryDefault();

// Load services
include APP_PATH . '/config/services.php';

// Get config from DI for the loader
$config = $di->getConfig();

// Load autoloader
include APP_PATH . '/config/loader.php';

// Set global DI container for models
\Phalcon\Di\Di::setDefault($di);

// Configure database with test environment variables
$config = $di->getConfig();
$di->setShared('db', function () use ($config) {
    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => getenv('DB_HOST') ?: $config->database->host,
        'username' => getenv('DB_USERNAME') ?: $config->database->username,
        'password' => getenv('DB_PASSWORD') ?: $config->database->password,
        'dbname'   => getenv('DB_DATABASE') ?: $config->database->dbname,
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    return new $class($params);
});
