<?php

$autoloadMap = [
    'Miho\\Segment\\' => '/segment/',
    'Miho\\Core\\' => '/core/'
];

function miho_autoloader($class)
{
    global $autoloadMap;

    $prefix = 'Miho\\';
    $base_dir = __DIR__ . '/../';
    $len = strlen($prefix);

    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $rclass = substr($class, $len);
    $mapped = [];

    foreach ($autoloadMap as $namespace => $path) {
        if (strncmp($namespace, $class, strlen($namespace)) == 0) {
            $mapped = [$namespace, $path];
            break;
        }
    }

    if (!empty($mapped)) {
        $rclass = str_replace($mapped[0], $mapped[1], $class);
    }

    $file = realpath($base_dir . str_replace('\\', '/', $rclass) . '.php');

    dump($file);

    if (file_exists($file)) {
        require $file;
    }
}

spl_autoload_register('miho_autoloader');
