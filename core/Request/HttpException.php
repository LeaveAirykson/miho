<?php

namespace App\Core\Request;

use RuntimeException;
use Throwable;

class HttpException extends RuntimeException
{

    protected $httpcode = 500;
    protected $response = null;

    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getHttpCode()
    {
        return $this->httpcode;
    }

    public function getResponse()
    {
        return $this->response;
    }
}
