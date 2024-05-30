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

    public function add(string $method, string $path, string $controller, array $guard = [])
    {
        if (!is_string($controller) || !is_string($path) || !is_string($method)) {
            throw new \RuntimeException('Invalid route set, missing parameters!');
        }

        if (isset($this->routes[$path][$method])) {
            throw new \RuntimeException('Controller for uri is already registered');
        }

        $this->routes[$path][$method] = new Route($method, $path, $controller, $guard);

        return $this;
    }

    public function getHandler(string $path, string $method)
    {
        $route = $this->findRoute($path, $method);

        if (is_null($route)) {
            throw new RouteNotFoundError("No matching route found for: $path ($method)");
        }

        return $route;
    }

    public function findRoute(string $path, string $method): null|Route
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

        return null;
    }

    public function getRoutes()
    {
        return $this->routes;
    }
}
