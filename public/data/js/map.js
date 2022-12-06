const markergreen = L.icon({
    iconUrl: './data/icons/marker-green.png',
    iconSize: [25, 40],
    iconAnchor: [17, 40],
    popupAnchor: [-13, -40],
    shadowUrl: './data/icons/marker-shadow.png',
    shadowSize: [25, 40],
    shadowAnchor: [17, 40],
});
const markerpurple = L.icon({
    iconUrl: './data/icons/marker-purple.png',
    iconSize: [25, 40],
    iconAnchor: [17, 40],
    popupAnchor: [-13, -40],
    shadowUrl: './data/icons/marker-shadow.png',
    shadowSize: [25, 40],
    shadowAnchor: [17, 40],
});
console.log(L.Icon.Default);


let map = L.map('map').setView([48.6880561, 6.1559293], 13);




L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
}).addTo(map);
fetch('https://geoservices.grand-nancy.org/arcgis/rest/services/public/VOIRIE_Parking/MapServer/0/query?where=1%3D1&text=&objectIds=&time=&geometry=&geometryType=esriGeometryEnvelope&inSR=&spatialRel=esriSpatialRelIntersects&relationParam=&outFields=nom%2Cadresse%2Cplaces%2Ccapacite&returnGeometry=true&returnTrueCurves=false&maxAllowableOffset=&geometryPrecision=&outSR=4326&returnIdsOnly=false&returnCountOnly=false&orderByFields=&groupByFieldsForStatistics=&outStatistics=&returnZ=false&returnM=false&gdbVersion=&returnDistinctValues=false&resultOffset=&resultRecordCount=&queryByDistance=&returnExtentsOnly=false&datumTransformation=&parameterValues=&rangeValues=&f=pjson'
).then(response => response.json()).then(data => {
    data.features.forEach(feature => {
        let marker = L.marker([feature.geometry.y, feature.geometry.x]).addTo(map);
        marker.bindPopup(feature.attributes.NOM + '<br>' + feature.attributes.ADRESSE + '<br>' + feature.attributes.PLACES + '/' + feature.attributes.CAPACITE + ' places');
    });
});
function onMapClick(e) {
    let marker =L.marker([ e.latlng.lat, e.latlng.lng], {icon : markerpurple});

    let label = prompt("Label");
    if(label){
        marker.bindPopup(label);
    }
    marker.addTo(map);
}


map.on('click', onMapClick);
