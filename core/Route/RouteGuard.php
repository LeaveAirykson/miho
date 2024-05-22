<?php

namespace App\Core\Route;

class RouteGuard
{

    protected RouteHandler $handler;

    public function __construct(RouteHandler $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    function authorized()
    {
    }

    function run()
    {
        if (!$this->handler->getGuard()) {
            return;
        }

        // @TODO: implement RouteGuard run
    }
}
