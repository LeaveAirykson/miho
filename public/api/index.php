<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../../bootstrap/boot.php';


$app = new App\Core\App();


// $app->route('GET', '/', 'AuthController::testuser');

// $app->route('POST', '/login', 'AuthController::login');

// $app->route('POST', '/', 'Default::default');

// $app->route('GET', '/document/all', 'Document\\Document::default');

$app->run();
