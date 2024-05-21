<?php

namespace App\Core\Auth;

use App\Core\Utility\HttpException;

class UserNotFoundError extends HttpException
{
    protected $httpcode = 404;
    protected $response = 'User wurde nicht gefunden!';
}
