<?php
use Core\Controllers\AssetController;

return [
    'assets/module/{module}/{type}/{file}' => [AssetController::class, 'serve']
];
