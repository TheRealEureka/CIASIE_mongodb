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
//console.log(L.Icon.Default);

let parkings = L.layerGroup([]);
let myPoints = L.layerGroup([]);
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
            txt += '<br>' + point.attributes.PLACES + '/' + point.attributes.CAPACITE + ' places';
        }
        marker.bindPopup(txt);
        parkings.addLayer(marker);
    });

    display_points()

});
function onMapClick(e) {
    let marker =L.marker([ e.latlng.lat, e.latlng.lng], {icon : markerpurple});
    myPoints.addLayer(marker);
    let label = prompt("Label");
    if(label){
     marker.bindPopup(label);

    }
    points.push(marker);
    display_points();
}

function display_points(){
    let html = '';
    for(let i = 0; i < points.length; i++){
        let point = points[i];
        if(point.getPopup()) {
            html += '<li>' + point.getPopup().getContent() + '</li>';
        }
    }
    document.getElementById('points').innerHTML = html;
}

let overlayMaps = {
    "Parkings": parkings,
    "Mes points": myPoints
};


L.control.layers({}, overlayMaps).addTo(map);

map.on('click', onMapClick);

