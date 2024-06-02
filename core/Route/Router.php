<?php

/**
 * Based on the great work from SteamPixel
 * https://github.com/steampixel/simplePHPRouter
 */

namespace App\Core\Route;

use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;
use App\Core\Route\Route;

class Router
{
    protected array $routes = [];
    public ?HttpRequest $request = null;
    public ?Route $current = null;

    function __construct()
    {
        $this->request = new HttpRequest();

        return $this;
    }

    private function validController(array|string|\Closure $controller)
    {
        return !$controller instanceof \Closure && in_array(gettype($controller), ['string', 'array']);
    }

    public function add(string $method, string $path, array|string|\Closure $controller, array $middleware = []): self
    {
        if (!is_string($path) || !is_string($method)) {
            throw new \RuntimeException('Invalid route passed: missing parameters for path or method!');
        }

        if (!$this->validController($controller)) {
            throw new \RuntimeException("Invalid controller passed to route $path");
        }

        if (isset($this->routes[$path][$method])) {
            throw new \RuntimeException("Controller for path $path is already registered");
        }

        $this->routes[$path][$method] = new Route($method, $path, $controller, $middleware);

        return $this;
    }

    public function loadRoute(string $path, string $method): self
    {
        // reset current
        $this->current = null;

        // return right away if its a direct match
        // otherwise proceed with regex matching
        $route = $this->routes[$path][$method] ?? null;

        if ($route) {
            $this->current = $route;
            return $this;
        }

        foreach ($this->routes as $_path => $methods) {
            // ignore every route path in which requested method
            // is not defined
            if (!isset($methods[$method])) {
                continue;
            }

            // try to find matching route by regex
            $pattern = preg_replace('/:[a-zA-Z0-9_]+/', '([a-zA-Z0-9_]+)', $_path);
            preg_match('~^' . $pattern . '$~', $path, $matches);

            // remove first entry as it always
            // includes whole matching string
            array_shift($matches);

            // ignore route if regex match fails
            if (!isset($matches[0])) {
                continue;
            }

            // assume a match and reference route
            $route = $methods[$method];

            // extract params from route
            preg_match_all('/:([a-zA-Z0-9]+)/', addslashes($path), $paramExtr);

            // map param key to value
            if (isset($paramExtr[1])) {
                $params = [];

                foreach ($paramExtr[1] as $idx => $key) {
                    $params[$key] = $matches[$idx];
                }

                // save params to matching route
                $route->params = $params;
            }

            $this->current = $route;

            return $this;
        }

        throw new RouteNotFoundError("No matching route found for: $path ($method)");
    }

    function callMiddleware()
    {
        foreach ($this->current->middleware as $middleware) {
            (self::resolveClass($middleware))->run($this);
        }

        return $this;
    }

    function callController()
    {
        $controller = $this->current->controller;

        if ($controller instanceof \Closure) {
            return $controller($this->request, new HttpResponse());
        }

        $controller = self::resolveClass($controller);
        $action = $this->current->action;

        return $controller->$action($this->request, new HttpResponse());
    }

    static function resolveClass(string $name)
    {
        $class = str_replace('/\\\/', '\\', $name);
        return new $class();
    }
}
