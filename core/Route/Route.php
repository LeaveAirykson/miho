<?php

namespace App\Core\Route;

use App\Core\Utility\ControllerResolver;

class Route
{
    private string $method;
    private string $path;
    private $controller;
    private ?string $action = null;
    private array $guard = [];
    private array $params = [];

    function __construct($method, $path, $controller, array $guard = [])
    {
        $this->setMethod($method);
        $this->setPath($path);
        $this->setController($controller);
        $this->setGuard($guard);

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
        $parts = explode('::', $controller);
        $this->controller = $parts[0];
        $this->action = $parts[1] ?? $this->action;
        return $this;
    }

    function getController()
    {
        return ControllerResolver::resolve($this->controller);
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

    function setGuard(?array $guard)
    {
        $this->guard = $guard;
        return $this;
    }

    function getGuard()
    {
        return $this->guard;
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
}
