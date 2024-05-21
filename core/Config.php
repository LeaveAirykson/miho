<?php

namespace App\Core;

class Config
{
    private static $config = null;
    const CONFIG_FILE_PATH = CONFIG_PATH . '/config.php';

    static function readConfig()
    {
        if (is_null(self::$config)) {
            $main = CONFIG_PATH . '/config.php';
            $local = CONFIG_PATH . '/config.local.php';
            $base = include $main;

            if (file_exists($local)) {
                $localconfig = include $local;
                $config = array_replace_recursive($base, $localconfig);
            }

            self::$config = json_decode(json_encode($config, JSON_FORCE_OBJECT), false);
        }
    }

    static function get($path)
    {
        self::readConfig();

        $path = explode('.', $path);
        $temp = &self::$config;

        foreach ($path as $key) {
            $temp = &$temp->{$key};
        }

        return $temp;
    }


    public function __get($key)
    {
        return self::get($key);
    }
}
