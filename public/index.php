<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \App\Core\Application();

// Register all controllers
$app->registerControllers([
    \App\Controllers\UsersController::class
]);

$app->run();