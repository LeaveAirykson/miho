<?php

namespace App\Core\Utility;


class StringHelper
{
    static function sanitizeUmlaut(string $str)
    {
        return str_replace(['ö', 'ä', 'ü', 'ß'], ['oe', 'ae', 'ue', 'ss'], $str);
    }

    static function convertToSlug(string $str)
    {
        $clean = strtolower($str);
        $clean = self::sanitizeUmlaut($clean);
        $clean = str_replace(' ', '-', $clean);
        return str_replace('/[^a-z_-]/', '', $clean);
    }

    static function minify(string $input)
    {
        return preg_replace(['/\s{2,}/', '/\n/'], ' ', $input);
    }
}
