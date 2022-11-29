<?php
$manager = new MongoDB\Driver\Manager("mongodb://
root:root@mongodb:27017");
try {
    $manager->executeCommand("initmongodb", new MongoDB\Driver\Command([
        "ping" => 1
    ]));
} catch (\MongoDB\Driver\Exception\Exception $e) {
    echo $e->getMessage();
}
var_dump($manager);

