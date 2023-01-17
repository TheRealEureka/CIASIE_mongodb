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
    $response->getBody()->write(json_encode(\App\Utils\Fetcher::fetchAll()));
    return $response;
});
$app->get('/test', function (Request $request, Response $response, $args) {
    $db = MongoConnector::makeConnection();
    $res = $db->movies->find( [] );
    $txt = "";
    foreach ($res->toArray() as $movie) {
        $txt .= $movie["titre"];
    }
    $response->getBody()->write($txt);
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