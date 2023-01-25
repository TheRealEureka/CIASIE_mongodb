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
    $view = \App\view\ViewManager::getView("index.html");
    $msg = "";
    if(isset($_SESSION['username'])){
        $msg = "Bonjour ".$_SESSION['username']." !";
    }
    $view = str_replace("{{UserMessage}}", $msg, $view);
    $response->getBody()->write($view);
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
     $view = \App\view\ViewManager::getView("login.html");
     $msg = "";
    if(isset($_SESSION['username'])){
        session_destroy();
        $msg = "<span style='color:green'>Vous avez bien été déconnecté.</span>";
    }
     $view = str_replace("{{ERROR}}", $msg, $view);
    $response->getBody()->write($view);
    return $response;
});
$app->post('/login', function (Request $request, Response $response, $args) {
    $msg = "";
    $login = Raccoon::loginUser($_POST, $msg);
    if($login){
        $_SESSION['username'] = $_POST['username'];
        return $response->withStatus(302)->withHeader('Location', '/');
    }
    $view = \App\view\ViewManager::getView("login.html");
    $view = str_replace("{{ERROR}}", $msg, $view);
    $response->getBody()->write($view);
    return $response;
});

$app->get('/register', function (Request $request, Response $response, $args) {
    $view = \App\view\ViewManager::getView("register.html");
    $view = str_replace("{{ERROR}}", "", $view);
    $response->getBody()->write($view);

    return $response;
});
$app->post('/register', function (Request $request, Response $response, $args) {
    $msg = "";
    $register = Raccoon::registerUser($_POST, $msg);
    if($register){
        $msg = "<span style='color:green'>Vous avez bien été inscrit. Veuillez vous connecter.</span>";
    }

    $view = \App\view\ViewManager::getView("register.html");
   $view = str_replace("{{ERROR}}", $msg, $view);
    $response->getBody()->write($view);
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