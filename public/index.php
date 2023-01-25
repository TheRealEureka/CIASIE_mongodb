<?php
namespace App;

session_start();

use App\Utils\MongoConnector;
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
$app->get('/initdata', function (Request $request, Response $response, $args) {
    $db = MongoConnector::makeConnection();
    $res = "Collection already exists";
    if(!MongoConnector::isCollectionExist('sites')){
        $db->createCollection('sites');
        $col = $db->selectCollection('sites');
        $col->insertMany(\App\Utils\Fetcher::fetchAll()['features']);
        $res = "Collection created, data inserted";
    }
    if(!MongoConnector::isCollectionExist('users')){
        $db->createCollection('users');
        $res = "Collection created, data inserted";
    }
    $response->getBody()->write($res);
    return $response;
});
$app->get('/deldata', function (Request $request, Response $response, $args) {
    $db = MongoConnector::makeConnection();
    $res = "Collection not exists";
    if(MongoConnector::isCollectionExist('sites')){
        $db->dropCollection('sites');
        $res = "Collection deleted";
    }
    if(MongoConnector::isCollectionExist('users')){
        $db->dropCollection('users');
    }

    $response->getBody()->write($res);
    return $response;
});

$app->get('/api/getData', function (Request $request, Response $response, $args) {
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

try {
    $app->run();
} catch (Throwable $e) {
    echo $e->getMessage();
}