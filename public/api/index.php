<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../../bootstrap/boot.php';

$app = new App\Core\App();

// $app->route('POST', '/login', 'AuthController::login');
$app->route('GET', '/', 'TestController', ['AuthGuard']);
$app->route('GET', '/testdoc', 'TestController::testdoc');
$app->route('GET', '/builddoc', 'TestController::builddoc');
$app->route('GET', '/testuser', 'TestController::testuser');
$app->route('GET', '/testdoc/:id', 'TestController::testdoc');

$app->run();
