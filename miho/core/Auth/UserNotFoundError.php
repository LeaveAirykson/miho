<?php

namespace Miho\Core\Auth;

use Miho\Core\Utility\SpError;

class UserNotFoundError extends SpError
{
    protected $httpcode = 404;
    protected $response = 'User wurde nicht gefunden!';
}
