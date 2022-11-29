<?php
namespace App;

session_start();

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Throwable;

require __DIR__ . '/../vendor/autoload.php';

$container = require_once __DIR__ . '/../bootstrap.php';

AppFactory::setContainer($container);

$app = AppFactory::create();



try {
    $app->run();
} catch (Throwable $e) {
    echo $e->getMessage();
}