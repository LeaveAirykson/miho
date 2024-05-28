<?php

namespace App\Core\Storage;

class ConstraintResolver
{
    public static function resolve($constraint)
    {
        $nsp = "App\\Core\\Storage\\Constraint\\";
        try {
            $path = $nsp . ucfirst($constraint) . "Constraint";
            $class = str_replace('/\\\/', '\\', $path);
            return new $class();
        } catch (\Throwable $th) {
            return null;
        }
    }
}
