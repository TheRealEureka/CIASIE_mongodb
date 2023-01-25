<?php
namespace App;
header("Access-Control-Allow-Headers: Content-Type");

session_start();

use App\Utils\MongoConnector;
use App\Utils\Raccoon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Throwable;

require '../vendor/autoload.php';

$app = AppFactory::create();
MongoConnector::setConfig('../src/conf/dbconf.ini');

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(\App\view\ViewManager::getView("index.html"));
    return $response;
});


$app->get('/api/getdata', function (Request $request, Response $response, $args) {
    $db = MongoConnector::makeConnection();
    $res = $db->sites->find( [] );

    $response->getBody()->write(json_encode($res->toArray(),true));
    return $response;
});

$app->get('/map', function (Request $request, Response $response, $args) {
    $response->getBody()->write(\App\view\ViewManager::getView("map.html"));
    return $response;
});
$app->get('/login', function (Request $request, Response $response, $args) {

    $response->getBody()->write(\App\view\ViewManager::getView("login.php"));
    return $response;
});
$app->post('/login', function (Request $request, Response $response, $args) {
    $response->getBody()->write(\App\view\ViewManager::getView("login.php"));
    return $response;
});
$app->get('/register', function (Request $request, Response $response, $args) {
    $response->getBody()->write(\App\view\ViewManager::getView("register.php"));
    return $response;
});
$app->post('/register', function (Request $request, Response $response, $args) {
    $response->getBody()->write(\App\view\ViewManager::getView("register.php"));
    return $response;
});
$app->post('/addPoint', function (Request $request, Response $response, $args) {
    $response->getBody()->write(json_encode(Raccoon::getPost(),true));
    return $response;
});
$app->post('/removePoint', function (Request $request, Response $response, $args) {
    $response->getBody()->write(json_encode(Raccoon::getPost(),true));
    return $response;
});

try {
    $app->run();
} catch (Throwable $e) {
    echo $e->getMessage();
}