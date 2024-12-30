<?php

namespace App\Core\Attributes;

class Route {
    public static function parseDocComment(string $docComment): ?array {
        if (preg_match('/@Route\((.*?)\)/', $docComment, $matches)) {
            $routeParams = [];
            $paramString = $matches[1];
            
            // Parse path
            if (preg_match('/path\s*=\s*"([^"]*)"/', $paramString, $pathMatch)) {
                $routeParams['path'] = $pathMatch[1];
            }
            
            // Parse method
            if (preg_match('/method\s*=\s*"([^"]*)"/', $paramString, $methodMatch)) {
                $routeParams['method'] = strtoupper($methodMatch[1]);
            }
            
            return $routeParams;
        }
        return null;
    }
}