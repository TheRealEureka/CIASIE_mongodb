<?php
require_once "../vendor/autoload.php" ;

$c = new \MongoDB\Client("mongodb://mongodb:27017");
$db = $c->initmongodb;
/**
$i=$db->movies->insertOne( [
    "titre"=>"joker",
    "annee"=>2019,
    "genre"=>"marvel",
    "directeur"=> ["nom"=>"Phillips", "prenom"=>"Todd"]
]);

$db->movies->deleteMany([]);

 **/

$res = $db->movies->find( [] ) ;
;
?>
<table>
    <thead>
        <tr>
            <th>Titre</th>
            <th>Année</th>
            <th>Genre</th>
            <th>Directeur</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach ($res->toArray() as $movie) {
    echo "<tr>";
    echo "<td>".$movie["titre"]."</td>";
    echo "<td>".$movie["annee"]."</td>";
    echo "<td>".$movie["genre"]."</td>";
    echo "<td>".$movie["directeur"]['nom']." ".$movie["directeur"]['prenom']."</td>";
    echo "</tr>";
}
?>
    </tbody>
</table>

