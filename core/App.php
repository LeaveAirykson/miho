<?php

namespace App\Core;

use App\Core\Route\Router;
use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;
use App\Core\Request\HttpException;
use App\Core\Session\Session;
use App\Core\Utility\Logger;
use Throwable;

class App
{
    public Router $router;
    public Session $session;

    function __construct()
    {
        $this->session = new Session();
        $this->router = new Router();
    }

    function run()
    {
        // workaround to handle preflight requests
        // for CORS situations in local vm
        if (Config::get('dev') && $this->getRequest()->getCurrentMethod() === HttpRequest::PREFLIGHT) {
            return (new HttpResponse())->status(204)->send();
        }

        try {
            $this->router
                ->loadRoute(
                    $this->getRequest()->getCurrentUri(),
                    $this->getRequest()->getCurrentMethod()
                )
                ->callMiddleware()
                ->callController();
        } catch (\Throwable $th) {
            return $this->handleError($th);
        }
    }


    function getRequest()
    {
        return $this->router->request;
    }

    /**
     * Define a route for GET method
     *
     * @param string $path
     * @param array|string|\Closure $controller
     * @param array $middleware
     * @return void
     */
    function get(string $path, array|string|\Closure $controller, array $middleware = []): void
    {
        $this->router->add(HttpRequest::GET, $path, $controller, $middleware);
    }

    /**
     * Define a route for POST method
     *
     * @param string $path
     * @param array|string|\Closure $controller
     * @param array $middleware
     * @return void
     */
    function post(string $path, array|string|\Closure $controller, array $middleware = []): void
    {
        $this->router->add(HttpRequest::POST, $path, $controller, $middleware);
    }

    /**
     * Define a route for POST method
     *
     * @param string $path
     * @param array|string|\Closure $controller
     * @param array $middleware
     * @return void
     */
    function put(string $path, array|string|\Closure $controller, array $middleware = []): void
    {
        $this->router->add(HttpRequest::PUT, $path, $controller, $middleware);
    }

    /**
     * Define a route for DELETE method
     *
     * @param string $path
     * @param array|string|\Closure $controller
     * @param array $middleware
     * @return void
     */
    function delete(string $path, array|string|\Closure $controller, array $middleware = []): void
    {
        $this->router->add(HttpRequest::POST, $path, $controller, $middleware);
    }

    /**
     * Define a route for preflight (OPTIONS method)
     *
     * @param string $path
     * @param array|string|\Closure $controller
     * @param array $middleware
     * @return void
     */
    function preflight(string $path, array|string|\Closure $controller, array $middleware = []): void
    {
        $this->router->add(HttpRequest::PREFLIGHT, $path, $controller, $middleware);
    }

    function handleError(Throwable | HttpException $th)
    {
        // log the whole error
        $msg = $th->getMessage();
        $response = new HttpResponse();

        if (Config::get('debug')) {
            $msg = [
                $th->getMessage(),
                $this->getRequest()->getCurrentMethod(),
                $this->getRequest()->getCurrentUri(),
                str_replace(["\r", "\n", ROOT_PATH], [' ', ' ', ''], $th->getTraceAsString()),
            ];
        }

        Logger::error($msg);

        // handle our http errors first
        if ($th instanceof HttpException) {
            return $response->status($th->getHttpCode())->json($th->getResponse());
        }

        // as a fallback use a 500 error and a generic message
        return $response->status(500)->json('Es ist ein Fehler aufgetreten!');
    }
}
