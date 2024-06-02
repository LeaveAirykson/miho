<?php

namespace App\Core\Request;

class ParamBag
{
    private array $params = [];

    function get($name)
    {
        return $this->params[$name] ?? null;
    }

    function set($name, $value)
    {
        $this->params[$name] = $value;
    }

    function all()
    {
        return $this->params;
    }
}
