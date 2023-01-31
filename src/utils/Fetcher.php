<?php

namespace App\Utils;

use Exception;

class Fetcher
{
    private const URL_PARKINGS ="https://geoservices.grand-nancy.org/arcgis/rest/services/public/VOIRIE_Parking/MapServer/0/query?where=1%3D1&text=&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=nom%2Cadresse%2Cplaces%2Ccapacite&returnGeometry=true&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=4326&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentsOnly=false&datumTransformation=&parameterValues=&rangeValues=&f=pjson";
    private const URL_VELOS ="https://api.jcdecaux.com/vls/v1/stations?contract=nancy&apiKey=b1977e9d41e23998327aaf468d3d0691196cc814";
    private const URL_TRANSPORTS ="https://transport-data-gouv-fr-resource-history-prod.cellar-c2.services.clever-cloud.com/conversions/gtfs-to-geojson/55795/55795.20221220.180715.388446.zip.geojson";
  public static function fetchAll(): array
  {
    $parkings = self::fetchParkings();
    $velos = self::fetchVelos();
    $transports = self::fetchTransports();
    return array("type"=> "FeatureCollection", "features" => array_merge($parkings, $velos, $transports));
  }
  private static function fetchParkings(): array
  {
      try{
          $data = json_decode(file_get_contents(self::URL_PARKINGS));
          $data = $data->features;
          $parkings = [];
          foreach ($data as $parking){
              $label = $parking->attributes->NOM . '<br>' . $parking->attributes->ADRESSE;
                if($parking->attributes->PLACES && $parking->attributes->CAPACITE){
                    $label .= '<br><br>' . $parking->attributes->PLACES . '/' . $parking->attributes->CAPACITE . ' places';
                }
              $parkings[] = array(
                  "type" => "Feature",
                  "geometry" => array(
                        "type" => "Point",
                        "coordinates" => array(
                            $parking->geometry->x,
                            $parking->geometry->y
                        )
                    ),
                    "properties" => array(
                        "name" => $parking->attributes->NOM,
                        "label" => $label,
                        "category" => "parking",
                        "opts" => array()
                    )
                  );
          }
          return $parkings;
      }catch (Exception $e){
          return [];
      }
  }
    private static function fetchVelos(): array
    {
        try {
            $data = json_decode(file_get_contents(self::URL_VELOS));
            $velos = [];
            foreach ($data as $velo){
                $label = $velo->name . '<br>' . $velo->address . '<br><br> La station est actuellement '. ($velo->status == "OPEN" ? "ouverte " : "fermée ") .'<br><br>'. $velo->available_bike_stands . '/' . $velo->bike_stands . ' emplacements disponibles <br>' . $velo->available_bikes . ' vélos disponibles';
                $velos[] = array(
                    "type" => "Feature",
                    "geometry" => array(
                        "type" => "Point",
                        "coordinates" => array(
                            $velo->position->lng,
                            $velo->position->lat
                        )
                    ),
                    "properties" => array(
                        "name" => $velo->name,
                        "label" => $label,
                        "category" => "velo",
                        "opts" => array(
                            "icon" => "markergreen"
                        )
                    )
                );
            }
            return $velos;
        }catch (Exception $e){
            return [];
        }

    }
    private static function fetchTransports(): array
    {
        try{
            $data = json_decode(file_get_contents(self::URL_TRANSPORTS));
            $data = $data->features;
            $transports = [];
            foreach ($data as $transport){

                if($transport->geometry->type !== "LineString"){
                    $options = array("icon"=>"markerround");
                    $name = $transport->properties->name;
                    $label = "Arrêt " . $transport->properties->name . '<br><br> Code de l\'arrêt : ' . $transport->properties->code;
                }else{
                    $name = $transport->properties->route_short_name;
                    $label = "Ligne " . $transport->properties->route_short_name . '<br><br>' . $transport->properties->route_long_name;
                    $options = array("color" => $transport->properties->route_color);
                }
                $transports[] = array(
                    "type" => "Feature",
                    "geometry" => $transport->geometry,
                    "properties" => array(
                        "name" => $name,
                        "label" => $label,
                        "category" => "transport",
                        "opts" => $options
                    )
                );
            }
            return $transports;
        }catch (Exception $e){
            return [];
        }
    }
}