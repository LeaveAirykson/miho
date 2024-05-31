<?php

use App\Controller\AuthController;
use App\Controller\TestController;
use App\Middleware\AuthGuard;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../../bootstrap/boot.php';

$app = new App\Core\App();

// $app->route('POST', '/login', 'AuthController::login');
$app->get('/', TestController::class, [AuthGuard::class]);
$app->get('/testdoc', [TestController::class, 'testdoc']);
$app->get('/builddoc', [TestController::class, 'builddoc']);
$app->get('/testuser', [TestController::class, 'testuser']);
$app->get('/testdoc/:id', [TestController::class, 'testdoc']);


// $app->get('/test', function (HttpRequest $req, HttpResponse $res) {
//     $data = ['does_it_work' => true];
//     return $res->json($data);
// });

$app->run();
