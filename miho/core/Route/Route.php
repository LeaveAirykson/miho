<?php

namespace Miho\Core\Route;

class Route
{

    public $route;
    public $params;

    function __construct()
    {
        $this->params = null;
        $this->route = null;
    }
}
