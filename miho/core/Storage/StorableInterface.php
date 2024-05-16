<?php

namespace Miho\Core\Storage;

interface StorableInterface
{
    static function get(string $id);
    static function getAll();
    static function getByPropertyValue(string $prop, $value, bool $single);
    static function remove(string $id);
    static function create(array $data);
    static function exists(string $id);
}
