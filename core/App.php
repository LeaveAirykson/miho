<?php

namespace App\Core;

use App\Core\Route\Router;
use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;
use App\Core\Request\HttpException;
use App\Core\Route\Route;
use App\Core\Utility\Logger;
use Throwable;

class App
{
    public ?HttpRequest $request = null;
    public Router $router;

    function __construct()
    {
        $this->router = new Router();
    }

    function run()
    {
        // workaround to handle preflight requests
        // for CORS situations in local vm
        if (Config::get('dev') && $this->getRequest()->getCurrentMethod() === 'OPTIONS') {
            return (new HttpResponse())->status(204)->send();
        }

        try {
            $route = $this->router->findRoute(
                $this->getRequest()->getCurrentUri(),
                $this->getRequest()->getCurrentMethod()
            );

            return $this->callRouteController($route);
        } catch (\Throwable $th) {
            return $this->handleError($th, new HttpResponse());
        }
    }

    public function callRouteController(Route $route)
    {
        $controller = $route->getController();
        $action = $route->getAction();

        // attach route params to request
        $this->getRequest()->setParams($route->getParams());

        // protect execution by guard
        // @TODO: Implement route guard
        // (new RouteGuard($route))->run();

        // execute controller action
        return $controller->$action($this->getRequest(), new HttpResponse());
    }

    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = new HttpRequest();
        }

        return $this->request;
    }


    public function route(string $method, string $route, string $controller, array $guard = [])
    {
        $this->router->add($method, $route, $controller, $guard);

        return $this;
    }

    public function handleError(Throwable | HttpException $th, HttpResponse $response)
    {
        // log the whole error
        $msg = $th->getMessage();

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
