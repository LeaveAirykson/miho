<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once __DIR__ . '/../../miho/bootstrap/boot.php';


$app = new Miho\Core\App();

// $app->route('POST', '/', 'Default::default');

$app->run();
