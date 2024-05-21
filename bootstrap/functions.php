<?php

function dump(...$args)
{
    echo "<pre>";

    foreach ($args as $arg) {
        var_dump($arg);
    }

    echo "</pre>";
}

function generateRandomString($length = 10, $space = false)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    if ($space) {
        $characters .= '     ';
    }

    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}
