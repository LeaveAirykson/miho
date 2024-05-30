<?php

namespace App\Core\Request;

class HttpRequest
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    private $authToken = null;
    private $params = [];

    /**
     * Get current request method from $_SERVER variable
     * @return string
     */
    public function getCurrentMethod()
    {
        if (!isset($_SERVER['REQUEST_METHOD'])) {
            throw new \RuntimeException('SERVER variable "REQUEST_METHOD" not set!');
        }
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Get request uri from $_SERVER variable
     * @return mixed
     */
    public function getCurrentUri()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            throw new \RuntimeException('SERVER variable "REQUEST_URI" not set!');
        }

        return preg_replace('/^\/api\/?/', '/', $_SERVER['REQUEST_URI']);
    }

    public function getHeaders()
    {
        return getallheaders();
    }

    public function setParams($params = [])
    {
        $this->params = $params;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParam(string $key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    public function getParam(string $key)
    {
        return $this->params[$key] ?? null;
    }

    public function getAuthToken()
    {
        if (array_key_exists('HTTP_AUTHORIZATION', $_SERVER) && preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            $this->authToken = $matches[1];
        }

        return $this->authToken;
    }

    /**
     * Get variables from HTTP
     * @return object
     */
    public function getVariables()
    {
        $variables = [];
        switch ($this->getCurrentMethod()) {
            case self::GET:
                $variables = $_GET;
                break;
            case self::POST:
                if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
                    $content = file_get_contents('php://input');
                    $variables = (array)json_decode($content);
                } else {
                    $variables = $_POST;
                }
                break;
            case self::DELETE:
            case self::PUT:
                $content = file_get_contents('php://input');
                if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
                    $variables = (array)json_decode($content);
                } else {
                    parse_str($content, $variables);
                }
                break;
        }

        return (object) $variables;
    }
}
