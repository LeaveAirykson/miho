<?php

namespace Miho\Core\Route;

use Miho\Core\Utility\SpError;

class RouteNotFoundError extends SpError
{
    protected $httpcode = 404;
    protected $response = 'Seite nicht gefunden!';
}
