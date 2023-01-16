let points = [];
const markergreen = L.icon({
    iconUrl: './data/icons/marker-green.png',
    iconSize: [25, 40],
    iconAnchor: [17, 40],
    popupAnchor: [-5, -40],
    shadowUrl: './data/icons/marker-shadow.png',
    shadowSize: [25, 40],
    shadowAnchor: [17, 40],
});

const markerpurple = L.icon({
    iconUrl: './data/icons/marker-purple.png',
    iconSize: [25, 40],
    iconAnchor: [17, 40],
    popupAnchor: [-5, -40],
    shadowUrl: './data/icons/marker-shadow.png',
    shadowSize: [25, 40],
    shadowAnchor: [17, 40],
});
const markerred= L.icon({
    iconUrl: './data/icons/marker-red.png',
    iconSize: [25, 40],
    iconAnchor: [17, 40],
    popupAnchor: [-5, -40],
    shadowUrl: './data/icons/marker-shadow.png',
    shadowSize: [25, 40],
    shadowAnchor: [17, 40],
});
//console.log(L.Icon.Default);

let parkings = L.layerGroup([]);
let velos = L.layerGroup([]);
let myPoints = L.layerGroup([]);
let transport = L.layerGroup([]);
let baselayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
})



let map = L.map('map',{
    layers: [baselayer, parkings, myPoints]
}).setView([48.6880561, 6.1559293], 13);

fetch('https://geoservices.grand-nancy.org/arcgis/rest/services/public/VOIRIE_Parking/MapServer/0/query?where=1%3D1&text=&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=nom%2Cadresse%2Cplaces%2Ccapacite&returnGeometry=true&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=4326&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentsOnly=false&datumTransformation=&parameterValues=&rangeValues=&f=pjson'
).then(response => response.json()).then(data => {
    data.features.forEach(point => {
        let marker = L.marker([point.geometry.y, point.geometry.x]);
        points.push(marker);
        let txt = point.attributes.NOM + '<br>' + point.attributes.ADRESSE;
        if(point.attributes.PLACES && point.attributes.CAPACITE){
            txt += '<br><br>' + point.attributes.PLACES + '/' + point.attributes.CAPACITE + ' places';
        }
        marker.bindPopup(txt);
        parkings.addLayer(marker);
    });

});
fetch('https://api.jcdecaux.com/vls/v1/stations?contract=nancy&apiKey=b1977e9d41e23998327aaf468d3d0691196cc814'
).then(response => response.json()).then(data => {
    data.forEach(point => {

        let marker = L.marker([point.position.lat, point.position.lng], {icon : markergreen});
        points.push(marker);
        let status = "ouverte";
        if(point.status !== 'OPEN'){
            status = "fermée";
        }
        let txt = point.name + '<br>' + point.address + '<br><br> La station est actuellement '+status + '</br>' +point.available_bike_stands +'/'+ point.bike_stands + ' emplacements disponibles <br>' + point.available_bikes + ' vélos disponibles';
        marker.bindPopup(txt);
        velos.addLayer(marker);
    });

});
fetch('https://transport-data-gouv-fr-resource-history-prod.cellar-c2.services.clever-cloud.com/conversions/gtfs-to-geojson/55795/55795.20221220.180715.388446.zip.geojson'
).then(response => response.json()).then(data => {
data.features.forEach(point => {
    console.log(point);
      let marker =  L.geoJSON(point, {
            style: function (feature) {
                if(feature.geometry.type == "LineString"){
                return {color: feature.properties.route_color};
                  }
            }
        });
      marker.bindPopup("Ligne "+point.properties.route_short_name + '</br></br>'+point.properties.route_long_name);
      transport.addLayer(marker);

})
});
function onMapClick(e) {
    let marker =L.marker([ e.latlng.lat, e.latlng.lng], {icon : markerpurple});
    myPoints.addLayer(marker);
    let label = prompt("Label");
    if(label){
     marker.bindPopup(label);

    }
    points.push(marker);
}



let overlayMaps = {
    "Parkings": parkings,
    "Stations de v\u00e9los": velos,
    "R\u00e9seau STAN": transport,
    "Mes points": myPoints
};


L.control.layers({}, overlayMaps).addTo(map);

//map.on('click', onMapClick);

