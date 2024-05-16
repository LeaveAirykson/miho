<?php

namespace Miho\Core;

class Config
{
    private static $config = null;
    const CONFIG_FILE_PATH = CONFIG_PATH . '/app.json';

    static function readConfig()
    {
        if (is_null(self::$config)) {
            self::$config = json_decode(file_get_contents(self::CONFIG_FILE_PATH));
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

    static function set($path, $value)
    {
        self::readConfig();

        $path = explode('.', $path);
        $temp = &self::$config;

        foreach ($path as $key) {
            $temp = &$temp->{$key};
        }

        $temp = $value;
    }

    public function __get($key)
    {
        return self::get($key);
    }

    public function __set($key, $value)
    {
        return self::set($key, $value);
    }
}
