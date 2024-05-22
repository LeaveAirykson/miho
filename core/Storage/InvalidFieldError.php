<?php

namespace App\Core\Storage;

use App\Core\Request\HttpException;

class InvalidFieldError extends HttpException
{
    protected $httpcode = 400;
    protected $response = 'Ungültige Feldwerte!';
}
