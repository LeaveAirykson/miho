<?php

namespace App\Core\Route;

use App\Core\Utility\HttpException;

class RouteNotFoundError extends HttpException
{
    protected $httpcode = 404;
    protected $response = 'Seite nicht gefunden!';
}
