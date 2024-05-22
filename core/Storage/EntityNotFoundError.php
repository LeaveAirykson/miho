<?php

namespace App\CoreStorage;

use App\Core\Request\HttpException;

class EntityFoundError extends HttpException
{
    protected $httpcode = 404;
    protected $response = 'Objekt wurde nicht gefunden!';
}
