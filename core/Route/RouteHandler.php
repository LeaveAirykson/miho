<?php

namespace App\Core\Route;

use App\Core\Utility\ClassResolver;

class RouteHandler
{
    protected $controller;
    protected $action;
    protected $guard = [];
    protected $params = [];

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     */
    public function setController($name)
    {
        $this->controller = ClassResolver::resolve($name);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParams($params)
    {
        return $this->params = $params;
        return $this;
    }

    public function getParam($key)
    {
        return $this->params[$key];
    }

    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function setGuard($guard)
    {
        $this->guard = $guard;
        return $this;
    }

    public function getGuard()
    {
        return $this->guard;
    }
}
