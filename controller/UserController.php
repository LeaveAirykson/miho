<?php

namespace App\Controller;

use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;
use App\Model\User;

class UserController
{

    public function getAll(HttpRequest $req, HttpResponse $res)
    {
        $data = User::get()->sortBy(['name', 'rank'])->getData();
        return $res->json($data);
    }
}
