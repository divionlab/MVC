<?php
define('BASE_PATH', dirname(__DIR__));
spl_autoload_register(function ($class) {
    $path = BASE_PATH . '/app/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

use Core\Security\CSRF;
if (class_exists(CSRF::class)) {
    CSRF::init();
}
require_once BASE_PATH . '/app/Core/Bootstrap.php';
use Core\Http\Request;
use Core\Http\Router;
use Core\Http\Response;
$request = new Request();
$router = new Router($request);
$response = $router->dispatch();
$response->send();