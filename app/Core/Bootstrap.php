<?php
use Core\Http\Middleware\SessionMiddleware;
use Core\Http\Middleware\LanguageMiddleware;
use Core\Http\Middleware\CsrfMiddleware;
use Core\Modules\ModuleLoader;
use Core\Http\Request;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/Helpers/functions.php';

$middlewares = [
    new SessionMiddleware(),
    new LanguageMiddleware(),
    new CsrfMiddleware(),
];

$request = new Request();

foreach ($middlewares as $middleware) {
    $middleware->handle($request);
}

$moduleLoader = new ModuleLoader();
$moduleLoader->loadModules(BASE_PATH . '/app/Modules');

$GLOBALS['moduleLoader'] = $moduleLoader;

// jei turi DB PDO instance – pakeisk $pdo
$pdo = $GLOBALS['pdo'] ?? null; // arba sukurk PDO čia
$GLOBALS['translator'] = new \Core\Localization\Translator($request, $pdo);
// patogu taip pat įdėti $request globalui
$GLOBALS['request'] = $request;

