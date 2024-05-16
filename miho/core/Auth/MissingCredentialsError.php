<?php

namespace Miho\Core\Auth;

use Miho\Core\Utility\SpError;


class MissingCredentialsError extends SpError
{
    protected $httpcode = 400;
    protected $response = 'Ungültige Logindaten!';
}
