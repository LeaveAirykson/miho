<?php

namespace App\Controller;

use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;
use App\Model\User;

class AuthController
{
    public function login(HttpRequest $req, HttpResponse $res)
    {
        $authData = User::login($req->getParam('email'), $req->getParam('password'));

        return $res->json($authData);
    }
}
