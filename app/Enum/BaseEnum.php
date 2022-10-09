<?php

namespace App\Enum;

use ReflectionClass;

abstract class BaseEnum
{
    public static function getValidValues(): array
    {
        $reflection = new ReflectionClass(static::class);
        return array_values($reflection->getConstants());
    }
}
