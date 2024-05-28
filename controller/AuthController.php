<?php

namespace App\Controller;

use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;
use App\Model\User;

class AuthController
{
    public function login(HttpRequest $req, HttpResponse $res)
    {
        $vars = $req->getVariables();
        $authData = User::login($vars->email, $vars->password);

        return $res->json($authData);
    }
}
