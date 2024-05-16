<?php

namespace Miho\Core\Storage;

use Miho\Core\Utility\SpError;

class InvalidFieldError extends SpError
{
    protected $httpcode = 400;
    protected $response = 'Ungültige Feldwerte!';
}
