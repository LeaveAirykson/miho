<?php

namespace App\Core\Auth;

use App\Core\Request\HttpException;


class MissingCredentialsError extends HttpException
{
    protected $httpcode = 400;
    protected $response = 'Ungültige Logindaten!';
}
