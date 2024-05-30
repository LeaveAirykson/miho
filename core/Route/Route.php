<?php

namespace App\Core\Route;

use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;

class Route
{
    private string $method;
    private string $path;
    private string $controller;
    private string $action = 'default';
    private array $middleware = [];
    private array $params = [];
    protected HttpRequest $request;

    function __construct(string $method, string $path, string $controller, array $middleware = [])
    {
        $this->setMethod($method);
        $this->setPath($path);
        $this->setController($controller);
        $this->setMiddleware($middleware);

        return $this;
    }

    function setMethod(string $method)
    {
        $this->method = trim(strtoupper($method));
        return $this;
    }

    function getMethod()
    {
        return $this->method;
    }

    function setController(string $controller)
    {
        $parts = explode("::", $controller);
        $this->controller = $parts[0];
        $this->action = $parts[1] ?? $this->action;

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
            (new ("App\\Middleware\\$middleware"))->run($this->request);
        }

        return $this;
    }

    function callController()
    {
        $controller = new ("App\\Controller\\" . $this->controller)();
        $action = $this->action;
        return $controller->$action($this->request, new HttpResponse());
    }
}
