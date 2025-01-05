<?php

namespace App\Core\Attributes;

class BasePath implements AttributeInterface {
    public static function getName(): string {
        return 'BasePath';
    }

    public static function parse(string $docComment): ?array {
        if (preg_match('/@BasePath\("([^"]+)"\)/', $docComment, $matches)) {         
            return ['path' => $matches[1]];
        }
        return null;
    }

    public function process(array $attributeData, callable $next) {
        return $next(); // Base path processing is handled during route registration
    }
}