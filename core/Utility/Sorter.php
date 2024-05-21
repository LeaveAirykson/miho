<?php

namespace App\Core\Utility;

class Sorter
{
    public static function sortObjectArrayByProp($array, $prop, $dir = 1)
    {
        $arr = $array;

        usort($arr, function ($a, $b) use ($dir, $prop) {
            $al = strtolower($a[$prop]);
            $bl = strtolower($b[$prop]);

            return ($dir) ? $al <=> $bl : $bl <=> $al;
        });

        return $arr;
    }
}
