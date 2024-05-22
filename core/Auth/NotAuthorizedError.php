<?php

namespace App\Core\Auth;

use App\Core\Request\HttpException;


class NotAuthorizedError extends HttpException
{
    protected $httpcode = 401;
    protected $response = 'Unzureichende Zugriffsrechte!';
}
