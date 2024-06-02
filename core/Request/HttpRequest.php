<?php

namespace App\Core\Request;

use App\Core\Session\Session;

class HttpRequest
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const PREFLIGHT = 'OPTIONS';

    private ?ParamBag $params;

    function __construct()
    {

        $this->extractAuthToken();
        $this->extractRequestParams();

        return $this;
    }

    private function extractAuthToken()
    {
        // extract auth token from authorization header
        if (array_key_exists('HTTP_AUTHORIZATION', $_SERVER) && preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            $authToken = $matches[1] ?? null;
            (new Session())->set('authToken', $authToken);
        }
    }

    private function extractRequestParams()
    {

        $this->params = new ParamBag();

        switch ($this->getCurrentMethod()) {
            case self::GET:
                $this->setParams($_GET);
                break;
            case self::POST:
                if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
                    $content = file_get_contents('php://input');
                    $this->setParams((array)json_decode($content));
                } else {
                    $this->setParams($_POST);
                }
                break;
            case self::DELETE:
            case self::PUT:
                $content = file_get_contents('php://input');
                if ($_SERVER['CONTENT_TYPE'] == 'application/json') {
                    $this->setParams((array)json_decode($content));
                } else {
                    parse_str(
                        $content,
                        $this->params
                    );
                }
                break;
        }
    }

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
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
    }

    public function getParams(): array
    {
        return $this->params->all();
    }

    public function setParam(string $key, $value)
    {
        $this->params->set($key, $value);

        return $this;
    }

    public function getParam(string $key)
    {
        return $this->params->get($key);
    }

    static function getMethods()
    {
        return [self::GET, self::POST, self::DELETE, self::PREFLIGHT, self::PUT];
    }
}
