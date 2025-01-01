<?php

namespace App\Core\Attributes;

class Auth {
    public static function parseDocComment(string $docComment): ?array {
        if (preg_match('/@Auth(\((.*?)\))?/', $docComment, $matches)) {
            return ['requireAuth' => true];
        }
        return null;
    }
}