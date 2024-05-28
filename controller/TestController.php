<?php

namespace App\Controller;

use App\Core\Builder\DocumentBuilder;
use App\Core\Request\HttpRequest;
use App\Core\Request\HttpResponse;
use App\Core\Utility\DebugTimer;
use App\Model\User;
use App\Model\Document;

class TestController
{
    public function testdoc(HttpRequest $req, HttpResponse $res)
    {
        // Document::create([
        //     'name' => 'Startseite',
        //     'author' => 'Geeg',
        //     'langcode' => 'de',
        //     'segments' => [
        //         [
        //             'type' => 'Text',
        //             'rank' => 0,
        //             'data' => [
        //                 'content' => '<h1>Willkommen auf der MIHO Testseite</h1>'
        //             ]
        //         ]
        //     ]
        // ]);

        $data = Document::get()->getData();

        return $res->json($data);
    }

    public function builddoc(HttpRequest $req, HttpResponse $res)
    {
        $doc = Document::getById('6655d22d703d8109336926');
        $timer = new DebugTimer();
        $builder = new DocumentBuilder();

        $timer->start();

        $builder->parse($doc)->build();

        $timer->end();

        $data = [
            'success' => true,
            'build_time' => $timer->result()
        ];

        return $res->json($data);
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

        $data = User::get(['active' => true])->getData();

        return $res->json($data);
    }
}
