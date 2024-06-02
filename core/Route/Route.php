<?php

namespace App\Core\Route;

use App\Core\Request\HttpRequest;
use App\Middleware\AuthGuard;

class Route
{
    public string $method;
    public string $path;
    public array|string|\Closure $controller;
    public string $action = 'default';
    public array $middleware = [];
    public array $params = [];
    protected HttpRequest $request;

    function __construct(string $method, string $path, array|string|\Closure $controller, array $middleware = [AuthGuard::class])
    {
        $method = trim(strtoupper($method));

        if (!in_array($method, HttpRequest::getMethods())) {
            throw new \InvalidArgumentException("$method is not allowed in Routing!");
        }

        $this->method = $method;
        $this->path = $path;
        $this->controller = $controller;
        $this->middleware = $middleware;

        if (is_array($controller)) {
            $this->controller = $controller[0];
            $this->action = $controller[1] ?? $this->action;
        }

        return $this;
    }


    function setParams(array $params = [])
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
}
