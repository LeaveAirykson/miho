<?php

namespace App\Middleware;

use App\Core\Route\Middleware;
use App\Core\Route\Router;
use App\Core\Utility\Logger;

class AuthGuard extends Middleware
{
    public function run(Router &$router)
    {
        Logger::log("Run AuthGuard::run()");

        $router->request->setParam('testing', true);
    }
}
