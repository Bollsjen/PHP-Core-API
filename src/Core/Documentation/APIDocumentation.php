<?php
// src/Core/Documentation/ApiDocumentation.php
namespace App\Core\Documentation;

class ApiDocumentation {
    private array $routes = [];

    public function addRoute(string $method, string $path, \ReflectionMethod $methodRef) {
        $docComment = $methodRef->getDocComment();
        $description = $this->parseDescription($docComment);
        $params = $this->parseParams($path);
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'description' => $description,
            'params' => $params,
            'controller' => $methodRef->getDeclaringClass()->getShortName(),
            'action' => $methodRef->getName()
        ];
    }

    private function parseDescription(string|false $docComment): string {
        if (!$docComment) return '';
        
        // Get the text between the first /** and the first @
        if (preg_match('/\/\*\*(.*?)(@|\*\/)/s', $docComment, $matches)) {
            return trim(str_replace('*', '', $matches[1]));
        }
        return '';
    }

    private function parseParams(string $path): array {
        $params = [];
        if (preg_match_all('/\{([^}]+)\}/', $path, $matches)) {
            foreach ($matches[1] as $param) {
                $params[] = [
                    'name' => $param,
                    'in' => 'path',
                    'required' => true
                ];
            }
        }
        return $params;
    }

    public function generateHtml(): string {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>API Documentation</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
                    line-height: 1.6;
                    margin: 0;
                    padding: 20px;
                    background: #f5f5f5;
                }
                .container {
                    max-width: 1200px;
                    margin: 0 auto;
                    background: white;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
                h1 {
                    color: #333;
                    border-bottom: 2px solid #eee;
                    padding-bottom: 10px;
                }
                .endpoint {
                    margin: 20px 0;
                    padding: 15px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                .method {
                    display: inline-block;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-weight: bold;
                    margin-right: 10px;
                }
                .get { background: #e7f5ff; color: #0066cc; }
                .post { background: #e3fcef; color: #00875a; }
                .put { background: #fff3e0; color: #995500; }
                .delete { background: #ffe8e8; color: #cc0000; }
                .path {
                    font-family: monospace;
                    font-size: 1.1em;
                }
                .params {
                    margin-top: 10px;
                    font-size: 0.9em;
                }
                .controller {
                    color: #666;
                    font-size: 0.9em;
                    margin-top: 5px;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>API Documentation</h1>';

        // Group by controller
        $groupedRoutes = [];
        foreach ($this->routes as $route) {
            $controller = $route['controller'];
            if (!isset($groupedRoutes[$controller])) {
                $groupedRoutes[$controller] = [];
            }
            $groupedRoutes[$controller][] = $route;
        }

        foreach ($groupedRoutes as $controller => $routes) {
            $html .= "<h2>{$controller}</h2>";
            foreach ($routes as $route) {
                $methodClass = strtolower($route['method']);
                $html .= '<div class="endpoint">';
                $html .= "<span class='method {$methodClass}'>{$route['method']}</span>";
                $html .= "<span class='path'>{$route['path']}</span>";
                
                if ($route['description']) {
                    $html .= "<p>{$route['description']}</p>";
                }
                
                if (!empty($route['params'])) {
                    $html .= '<div class="params"><strong>Parameters:</strong><ul>';
                    foreach ($route['params'] as $param) {
                        $html .= "<li>{$param['name']} (in {$param['in']})";
                        if ($param['required']) {
                            $html .= " <strong>required</strong>";
                        }
                        $html .= "</li>";
                    }
                    $html .= '</ul></div>';
                }
                
                $html .= "<div class='controller'>{$route['controller']}::{$route['action']}</div>";
                $html .= '</div>';
            }
        }

        $html .= '</div></body></html>';
        return $html;
    }
}