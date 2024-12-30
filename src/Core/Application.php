<?php

// src/Core/Application.php
namespace App\Core;

class Application {
    private Router $router;
    
    public function __construct() {
        $this->router = new Router();
    }
    
    public function getRouter(): Router {
        return $this->router;
    }
    
    public function registerControllers(array $controllers) {
        $this->router->registerControllers($controllers);
    }
    
    public function run() {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        $this->router->dispatch($method, $uri);
    }
}