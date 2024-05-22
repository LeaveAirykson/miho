<?php

namespace App\Core\Auth;

use App\Core\Request\HttpException;

class UserNotFoundError extends HttpException
{
    protected $httpcode = 404;
    protected $response = 'User wurde nicht gefunden!';
}
