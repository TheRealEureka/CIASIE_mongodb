<?php
require_once "../vendor/autoload.php" ;

$c = new \MongoDB\Client("mongodb://mongodb:27017");
$db = $c->initmongodb;

$i=$db->movies->insertOne( [
    "titre"=>"joker",
    "annee"=>2019,
    "genre"=>"marvel",
    "directeur"=> ["nom"=>"Phillips", "prenom"=>"Todd"]
]);
print $i->getInsertedId();


$res = $db->movies->find( [] ) ;
$res->next();
print_r($res);