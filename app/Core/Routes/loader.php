<?php
$routes = [];
$coreRoutesPath = BASE_PATH . '/app/Core/Routes';
foreach (glob($coreRoutesPath . '/*.php') as $routeFile) {
    if (basename($routeFile) === 'loader.php')
        continue;
    $fileRoutes = include $routeFile;
    if (is_array($fileRoutes)) {
        $routes = array_merge($routes, $fileRoutes);
    }
}

$modulesPath = BASE_PATH . '/app/Modules';
foreach (glob($modulesPath . '/*/routes.php') as $routeFile) {
    $moduleRoutes = include $routeFile;
    if (is_array($moduleRoutes)) {
        $routes = array_merge($routes, $moduleRoutes);
    }
}
return $routes;
