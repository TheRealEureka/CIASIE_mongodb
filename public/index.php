<?php
namespace App;
header("Access-Control-Allow-Headers: Content-Type");

session_start();

use App\Utils\Fetcher;
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
    $msg = "";
    $conWord = "Connexion";
    $disp = "";
    if(isset($_SESSION['username'])){
        $msg = "Bonjour ".$_SESSION['username']." !";
        $conWord = "Déconnexion";
        $disp = "style='display:none'";
    }
    $response->getBody()->write( \App\view\ViewManager::getView("index.html", array("UserMessage" => $msg, "loginWord" => $conWord, "disp"=>$disp)));
    return $response;
});


$app->get('/api/getdata', function (Request $request, Response $response, $args) {
    $db = MongoConnector::makeConnection();
    $res = $db->sites->find( [] );
    $points = [];
    if(isset($_SESSION['username'])){
        $username = $_SESSION['username'];
        $collection = $db->selectCollection('users');
        $user = $collection->findOne(['username' => $username]);
        if($user){
            $points = (array) $user['sites'];
        }
    }
    $response->getBody()->write(json_encode(array_merge($res->toArray(), $points),true));
    return $response;
});

$app->get('/map', function (Request $request, Response $response, $args) {
    $msg = "Vous n'êtes pas connecté, les points que vous ajouterez ne seront pas sauvegardés.";
    if(isset($_SESSION['username'])){
        $msg = "Bonjour ".$_SESSION['username']." ! ";
    }
    $response->getBody()->write(\App\view\ViewManager::getView("map.html", array("UserMessage" => $msg)));
    return $response;
});
$app->get('/login', function (Request $request, Response $response, $args) {
    $msg = "";
    if (isset($_SESSION['username'])) {
        session_destroy();
        $msg = "<span style='color:green'>Vous avez bien été déconnecté.</span>";
    }
    $response->getBody()->write(\App\view\ViewManager::getView("login.html", array("ERROR" => $msg)));

    return $response;
});
$app->post('/login', function (Request $request, Response $response, $args) {
    $msg = "";
    $login = Raccoon::loginUser($_POST, $msg);
    if($login){
        $_SESSION['username'] = $_POST['username'];
        return $response->withStatus(302)->withHeader('Location', '/');
    }
    $response->getBody()->write(\App\view\ViewManager::getView("login.html", array("ERROR" => $msg)));
    return $response;
});

$app->get('/register', function (Request $request, Response $response, $args) {
    $response->getBody()->write(\App\view\ViewManager::getView("register.html"));
    return $response;
});
$app->post('/register', function (Request $request, Response $response, $args) {
    $msg = "";
    $register = Raccoon::registerUser($_POST, $msg);
    if($register){
        $msg = "<span style='color:green'>Vous avez bien été inscrit. Veuillez vous connecter.</span>";
    }
    $response->getBody()->write(\App\view\ViewManager::getView("register.html", array("ERROR" => $msg, "username" => $_POST['username'] ?? "", "email" => $_POST['email']??"")));
    return $response;
});
$app->post('/addPoint', function (Request $request, Response $response, $args) {
    $post = Raccoon::getPost();
    $msg = "";
    if(isset($_SESSION['username'])){
        $post['username'] = $_SESSION['username'];
        Raccoon::addPoint($post, $msg);
    }
    $response->getBody()->write($msg);
    return $response;
});
$app->post('/removePoint', function (Request $request, Response $response, $args) {
    $post = Raccoon::getPost();
    if(isset($_SESSION['username'])){
        $post['username'] = $_SESSION['username'];
        Raccoon::removePoint($post, $msg);
    }
    $response->getBody()->write(json_encode("{}",true));
    return $response;
});

try {
    $app->run();
} catch (Throwable $e) {
    echo $e->getMessage();
}