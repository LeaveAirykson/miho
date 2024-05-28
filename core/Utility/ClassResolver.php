<?php

namespace App\Core\Utility;

class ClassResolver
{

    const API_CONTROLLER_NAMESPACE = 'App\\Controller\\';

    static function resolve($name)
    {
        $className = $name;

        // assume its already namespaced
        // if regex matches
        if (!preg_match('/([A-Z]{1}[a-zA-z]+\\\)/', $name)) {
            $className = self::API_CONTROLLER_NAMESPACE . $name;
        }

        try {
            $namespacedClass = self::sanitizePath($className);
            return new $namespacedClass();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function sanitizePath($path)
    {
        return str_replace('/\\\/', '\\', $path);
    }
}
