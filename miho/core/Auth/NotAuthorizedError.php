<?php

namespace Miho\Core\Auth;

use Miho\Core\Utility\SpError;


class NotAuthorizedError extends SpError
{
    protected $httpcode = 401;
    protected $response = 'Unzureichende Zugriffsrechte!';
}
