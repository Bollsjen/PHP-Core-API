<?php

namespace App\Core;

class Router {
    private array $routes = [];
    private array $params = [];

    public function registerControllers(array $controllers) {
        foreach ($controllers as $controller) {
            $this->registerController($controller);
        }
    }

    private function registerController($controllerClass) {
        $reflection = new \ReflectionClass($controllerClass);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        
        $controllerInstance = new $controllerClass();
        
        foreach ($methods as $method) {
            $docComment = $method->getDocComment();
            if ($docComment) {
                $routeParams = Attributes\Route::parseDocComment($docComment);
                if ($routeParams) {
                    $this->addRoute(
                        $routeParams['method'] ?? 'GET',
                        $routeParams['path'],
                        [$controllerInstance, $method->getName()]
                    );
                }
            }
        }
    }

    public function addRoute(string $method, string $path, array $handler) {
        $pattern = preg_replace('/\{([^}]+)\}/', '(?<$1>[^/]+)', $path);
        $pattern = "@^" . $pattern . "$@D";
        $this->routes[$method][$pattern] = $handler;
    }

    public function dispatch(string $method, string $uri) {
        $path = parse_url($uri, PHP_URL_PATH);
        
        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $handler) {
                if (preg_match($pattern, $path, $matches)) {
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) {
                            $this->params[$key] = $value;
                        }
                    }
                    return $this->executeHandler($handler);
                }
            }
        }
        
        http_response_code(404);
        return json_encode(['error' => 'Route not found']);
    }

    private function executeHandler(array $handler) {
        [$controllerInstance, $method] = $handler;
        $requestData = $this->getRequestData();
        $params = array_merge($this->params, $requestData);
        $response = call_user_func_array([$controllerInstance, $method], [$params]);
        
        if ($response !== null) {
            echo $response;
        }
    }

    private function getRequestData(): array {
        $data = [];
        
        if (!empty($_GET)) {
            $data = array_merge($data, $_GET);
        }
        
        $contentType = $_SERVER["CONTENT_TYPE"] ?? '';
        $requestBody = file_get_contents("php://input");
        
        if (strpos($contentType, 'application/json') !== false) {
            $jsonData = json_decode($requestBody, true);
            if ($jsonData) {
                $data = array_merge($data, $jsonData);
            }
        } elseif (!empty($_POST)) {
            $data = array_merge($data, $_POST);
        }
        
        return $data;
    }
}