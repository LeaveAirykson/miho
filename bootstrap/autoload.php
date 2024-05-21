<?php

$autoloadMap = [
    'App\\Core\\' => '/core/',
    'App\\Controller\\' => '/controller/',
    'App\\Model\\' => '/model/',
    'App\\Segment\\' => '/segment/',
    'App\\Service\\' => '/service/',
    'Firebase\\JWT\\' => '/vendor/firebase/php-jwt/src/'
];

function miho_autoloader($class)
{
    global $autoloadMap;
    $base_dir = __DIR__ . '/../';


    $mapped = [];

    foreach ($autoloadMap as $namespace => $path) {
        if (strncmp($namespace, $class, strlen($namespace)) == 0) {
            $mapped = [$namespace, $path];
            break;
        }
    }

    if (!empty($mapped)) {
        $rclass = str_replace($mapped[0], $mapped[1], $class);
    } else {
        $prefix = 'App\\';
        $len = strlen($prefix);

        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }
        $rclass = substr($class, $len);
    }

    $file = realpath($base_dir . str_replace('\\', '/', $rclass) . '.php');

    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('miho_autoloader');
