<?php

use App\Controller\AuthController;
use App\Middleware\AuthGuard;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../../bootstrap/boot.php';

$app = new App\Core\App();

// $app->route('POST', '/login', 'AuthController::login');
$app->get('/', 'TestController', ['AuthGuard']);
$app->get('/testdoc', 'TestController::testdoc');
$app->get('/builddoc', 'TestController::builddoc');
$app->get('/testuser', 'TestController::testuser');
$app->get('/testdoc/:id', 'TestController::testdoc');

$app->get('/testdoc/:id', AuthController::class);
$app->get('/testdoc/:id', [AuthController::class, 'login'], [AuthGuard::class]);


// $app->get('/test', function (HttpRequest $req, HttpResponse $res) {
//     $data = ['does_it_work' => true];
//     return $res->json($data);
// });

$app->run();
