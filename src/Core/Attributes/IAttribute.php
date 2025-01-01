<?php

namespace App\Core\Attributes;

interface IAttribute {
    public static function getName(): string;
    public static function parse(string $docComment): ?array;
    public function process(array $attributeData, callable $next);
}