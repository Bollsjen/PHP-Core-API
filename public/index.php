<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \App\Core\Application();

$app->enableDocs(true);

// Register all controllers
$app->registerControllers([
    \App\Controllers\UsersController::class,
    \App\Controllers\AuthController::class,
    \App\Controllers\MoviesController::class
]);

$app->run();