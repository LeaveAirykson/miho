<?php

namespace App\Core\Route;

use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;

class Route
{
    const CONTROLLER_NAMESPACE = "App\\Controller\\";
    const MIDDLEWARE_NAMESPACE = "App\\Middleware\\";

    private string $method;
    private string $path;
    private string|\Closure $controller;
    private string $action = 'default';
    private array $middleware = [];
    private array $params = [];
    protected HttpRequest $request;

    function __construct(string $method, string $path, array|string|\Closure $controller, array $middleware = [])
    {
        $this->setMethod($method);
        $this->setPath($path);
        $this->setController($controller);
        $this->setMiddleware($middleware);

        return $this;
    }

    function setMethod(string $method)
    {
        $method = trim(strtoupper($method));

        if (!in_array($method, HttpRequest::getMethods())) {
            throw new \InvalidArgumentException("$method is not allowed in Routing!");
        }

        $this->method = $method;

        return $this;
    }

    function getMethod()
    {
        return $this->method;
    }

    function setController(array|string|\Closure $controller)
    {
        if (is_array($controller)) {
            $this->controller = $controller[0];
            $this->action = $controller[1] ?? $this->action;

            return $this;
        }

        if (is_string($controller)) {
            $parts = explode("::", $controller);
            $this->controller = $parts[0];
            $this->action = $parts[1] ?? $this->action;

            return $this;
        }

        $this->controller = $controller;

        return $this;
    }

    function getController()
    {
        return $this->controller;
    }

    function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    function getAction()
    {
        return $this->action;
    }

    function setParams(?array $params = null)
    {
        $this->params = $params;

        return $this;
    }

    function getParams()
    {
        return $this->params;
    }

    function setParam(string $name, $value)
    {
        $this->params[$name] = $value;

        return $this;
    }

    function getParam(string $name)
    {
        return $this->params[$name] ?? null;
    }

    function setMiddleware(?array $middleware)
    {
        $this->middleware = $middleware;

        return $this;
    }

    function getMiddleware()
    {
        return $this->middleware;
    }

    function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    function getPath()
    {
        return $this->path;
    }

    function setRequest(HttpRequest &$request)
    {
        $this->request = $request;

        return $this;
    }

    function callMiddleware()
    {
        foreach ($this->getMiddleware() as $middleware) {
            (self::resolveMiddleware($middleware))->run($this->request);
        }

        return $this;
    }

    function callController()
    {
        $controller = $this->controller;

        if ($controller instanceof \Closure) {
            return $controller($this->request, new HttpResponse());
        }

        $controller = self::resolveController($controller);
        $action = $this->action;

        return $controller->$action($this->request, new HttpResponse());
    }

    static function resolveController(string $controller)
    {
        return self::resolve($controller, self::CONTROLLER_NAMESPACE);
    }

    static function resolveMiddleware(string $middleware)
    {
        return self::resolve($middleware, self::MIDDLEWARE_NAMESPACE);
    }

    static function resolve(string $name, $ns)
    {
        $className = $name;

        // prefix with namespace if missing
        if (!self::isNamespaced($name)) {
            $className = $ns . $name;
        }

        $class = str_replace('/\\\/', '\\', $className);
        return new $class();
    }

    static function isNamespaced(string $name)
    {
        return preg_match('/([A-Z]{1}[a-zA-z]+\\\)/', $name);
    }
}
