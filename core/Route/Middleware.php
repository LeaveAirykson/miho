<?php

namespace App\Core\Route;

use App\Core\Request\HttpRequest;

abstract class Middleware
{
    abstract public function run(HttpRequest &$req);
}
