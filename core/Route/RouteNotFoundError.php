<?php

namespace App\Core\Route;

use App\Core\Request\HttpException;

class RouteNotFoundError extends HttpException
{
    protected $httpcode = 404;
    protected $response = 'Seite nicht gefunden!';
}
