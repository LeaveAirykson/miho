<?php

namespace App\Controller;

use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;
use App\Model\User;
use App\Service\AuthService;

class AuthController
{
    public function login(HttpRequest $req, HttpResponse $res)
    {

        $vars = $req->getVariables();
        $authData = AuthService::login($vars->email, $vars->password);

        return $res->json($authData);
    }

    public function testuser(HttpRequest $req, HttpResponse $res)
    {
        // User::create([
        //     'name' => 'Hendl Beno3',
        //     'email' => 'hendl3@shortpants.geeg',
        //     'password' => 'testtest',
        //     'rank' => 10
        // ]);

        // User::create([
        //     'name' => 'Hendl Beno10',
        //     'email' => 'hendl10@shortpants.geeg',
        //     'password' => 'testtest',
        // ]);

        // User::create([
        //     'name' => 'Hendl Beno2',
        //     'email' => 'hendl2@shortpants.geeg',
        //     'password' => 'testtest',
        // ]);

        $data = User::get([])
            ->sortBy('rank:desc')
            ->getData();

        return $res->json($data);
    }
}
