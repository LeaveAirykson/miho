<?php

/**
 * Based on the great work from SteamPixel
 * https://github.com/steampixel/simplePHPRouter
 */

namespace App\Core\Route;

use App\Core\Route\Route;

class Router
{
    protected array $routes = [];

    private function validController(array|string|\Closure $controller)
    {
        return !$controller instanceof \Closure && in_array(gettype($controller), ['string', 'array']);
    }

    public function add(string $method, string $path, array|string|\Closure $controller, array $middleware = []): void
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
    }

    public function loadRoute(string $path, string $method): Route
    {
        // return right away if its a direct match
        // otherwise proceed with regex matching
        $route = $this->routes[$path][$method] ?? null;

        if ($route) {
            return $route;
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

            return $route;
        }

        throw new RouteNotFoundError("No matching route found for: $path ($method)");
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
