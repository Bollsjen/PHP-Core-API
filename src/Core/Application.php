<?php

// src/Core/Application.php
namespace App\Core;

class Application {
    private Router $router;
    private bool $docsEnabled = false;
    
    public function __construct() {
        $this->router = new Router();
    }
    
    public function getRouter(): Router {
        return $this->router;
    }

    public function enableDocs(bool $enable = true): void {
        $this->docsEnabled = $enable;
    }
    
    public function registerControllers(array $controllers) {
        $this->router->registerControllers($controllers);
    }
    
    public function run() {
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];

        // Check if docs are requested
        if ($this->docsEnabled && $uri === '/api/docs') {
            header('Content-Type: text/html; charset=utf-8');
            echo $this->generateDocsHtml($this->router->getRoutes());
            return;
        }
        
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }
        
        $this->router->dispatch($method, $uri);
    }

    private function generateDocsHtml(array $routes): string {
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <title>API Documentation</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: system-ui, -apple-system, sans-serif; line-height: 1.5; margin: 0; padding: 20px; background: #f5f5f5; }
                .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
                h1 { color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
                h2 { color: #444; margin-top: 30px; }
                .endpoint { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
                .method { display: inline-block; padding: 4px 8px; border-radius: 4px; font-weight: bold; margin-right: 10px; }
                .get { background: #e7f5ff; color: #0066cc; }
                .post { background: #e3fcef; color: #00875a; }
                .put { background: #fff3e0; color: #995500; }
                .delete { background: #ffe8e8; color: #cc0000; }
                .path { font-family: monospace; font-size: 1.1em; }
                .description { margin: 10px 0; color: #666; }
                .auth { color: #d63384; font-size: 0.9em; margin-top: 5px; }
                .controller { color: #666; font-size: 0.9em; margin-top: 5px; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>API Documentation</h1>';

        // Group routes by controller
        $groupedRoutes = [];
        foreach ($routes as $method => $methodRoutes) {
            foreach ($methodRoutes as $pattern => $handler) {
                $reflection = new \ReflectionMethod($handler[0], $handler[1]);
                $controllerName = get_class($handler[0]);
                $controllerName = substr($controllerName, strrpos($controllerName, '\\') + 1);
                
                if (!isset($groupedRoutes[$controllerName])) {
                    $groupedRoutes[$controllerName] = [];
                }

                $docComment = $reflection->getDocComment();
                $description = '';
                $auth = false;

                if ($docComment) {
                    // Extract description (text before first @)
                    if (preg_match('/\/\*\*(.*?)(@|\*\/)/s', $docComment, $matches)) {
                        $description = trim(str_replace(['/**', '*/', '*'], '', $matches[1]));
                    }
                    // Check for @Auth
                    $auth = strpos($docComment, '@Auth') !== false;
                }

                // Clean up the pattern
                $cleanPath = str_replace(['@^', '$@D'], '', $pattern);
                $cleanPath = preg_replace('/\(\?<([^>]+)>[^\)]+\)/', '{$1}', $cleanPath);

                $groupedRoutes[$controllerName][] = [
                    'method' => $method,
                    'path' => $cleanPath,
                    'description' => $description,
                    'auth' => $auth,
                    'action' => $handler[1]
                ];
            }
        }

        foreach ($groupedRoutes as $controller => $endpoints) {
            $html .= "<h2>{$controller}</h2>";
            foreach ($endpoints as $endpoint) {
                $methodClass = strtolower($endpoint['method']);
                $html .= '<div class="endpoint">';
                $html .= "<span class='method {$methodClass}'>{$endpoint['method']}</span>";
                $html .= "<span class='path'>{$endpoint['path']}</span>";
                
                if ($endpoint['description']) {
                    $html .= "<div class='description'>{$endpoint['description']}</div>";
                }
                
                if ($endpoint['auth']) {
                    $html .= "<div class='auth'>🔒 Requires authentication</div>";
                }

                $html .= "<div class='controller'>{$controller}::{$endpoint['action']}</div>";
                $html .= '</div>';
            }
        }

        $html .= '</div></body></html>';
        return $html;
    }
}
