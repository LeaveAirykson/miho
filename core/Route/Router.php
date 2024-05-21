<?php

/**
 * Based on the great work from SteamPixel
 * https://github.com/steampixel/simplePHPRouter
 */

namespace App\Core\Route;

use App\Core\Route\Route;

class Router
{
    private static $routes = [];

    public static function add($method = null, $route = null, $controller = null, $guard = null)
    {

        if (is_null($controller) || is_null($route) || is_null($method)) {
            throw new \RuntimeException('Invalid route set, missing parameters!');
        }

        if (isset(self::$routes[$route][$method])) {
            throw new \RuntimeException('Controller for uri is already registered');
        }

        self::$routes[$route][$method] = [$controller, $guard];
    }

    public static function read($uri, $method = false)
    {
        $route = self::matchesRoute($uri);
        $matching = null;

        if (isset(self::$routes[$route->route][$method])) {
            $matching = self::makeMatch($route, $method);
        }

        if (is_null($matching)) {
            throw new RouteNotFoundError('No matching route found for: ' . $uri);
        }

        return $matching;
    }

    protected static function matchesRoute($uri)
    {
        $matchingRoute = new Route();

        foreach (self::$routes as $route => $options) {
            // return right away if uri directly matches
            // otherwise proceed with regex matching
            if ($route == $uri) {
                $matchingRoute->route = $route;
                break;
            }

            // create regex pattern
            $pattern = preg_replace('/:[a-zA-Z0-9]+/', '([a-zA-Z0-9]+)', $route);

            // try to find matching route by regex
            preg_match('~^' . $pattern . '$~', $uri, $matches);

            // remove first entry as it always
            // includes whole matching string
            array_shift($matches);

            // return route data with attached
            // params if they exists
            if (isset($matches[0])) {
                // extract params from route
                preg_match_all('/:([a-zA-Z0-9]+)/', addslashes($route), $paramExtr);

                // map param key to value
                if (isset($paramExtr[1])) {
                    $params = null;

                    foreach ($paramExtr[1] as $idx => $key) {
                        $params[$key] = $matches[$idx];
                    }

                    // save params to matching route
                    $matchingRoute->params = $params;
                }

                // finally save route path
                $matchingRoute->route = $route;
                break;
            }
        }

        return $matchingRoute;
    }

    protected static function makeMatch(Route $route, $method = false)
    {
        $routemap = self::$routes[$route->route][$method];
        $parts = explode('::', $routemap[0]);

        $handler = new RouteHandler();
        $handler->setController($parts[0]);
        $handler->setAction($parts[1]);
        $handler->setParams($route->params);
        $handler->setGuard($routemap[1]);

        return $handler;
    }

    public static function getAll()
    {
        return self::$routes;
    }
}
