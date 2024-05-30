<?php

namespace App\Middleware;

use App\Core\Request\HttpRequest;
use App\Core\Route\Middleware;
use App\Core\Utility\Logger;

class AuthGuard extends Middleware
{
    public function run(HttpRequest &$req)
    {
        Logger::log("Run AuthGuard::run()");

        $req->setParam('testing', true);
    }
}
