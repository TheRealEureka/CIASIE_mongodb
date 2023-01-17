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

try {
    $app->run();
} catch (Throwable $e) {
    echo $e->getMessage();
}