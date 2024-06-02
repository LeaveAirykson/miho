<?php

namespace App\Core\Route;


abstract class Middleware
{
    abstract public function run(Router &$router);
}
