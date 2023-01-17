let points = [];

//Transferer les api call en php -> mongoDB
//Ajouter les points sur la map via l'api

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
const markerround= L.icon({
    iconUrl: './data/icons/marker-round.png',
    iconSize: [10, 10],
    iconAnchor: [5, 5],
    popupAnchor: [0, -7],
    shadowUrl: '',
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
    layers: [baselayer, parkings, myPoints, transport]
}).setView([48.6880561, 6.1559293], 13);

fetch('http://localhost:8080/api/getData'
).then(response => response.json()).then(data => {
    data.forEach(point => {
        let opt = {}
        if(point.properties['opts']['icon'] === 'markergreen'){
            opt.pointToLayer= function (feature, latlng) {
                    return L.marker(latlng, {icon: markergreen});
            }
        }else if(point.properties['opts']['icon'] === 'markerred'){
            opt.pointToLayer= function (feature, latlng) {
                    return L.marker(latlng, {icon: markerred});
                }

        } else if(point.properties['opts']['icon'] === 'markerround'){
            opt.pointToLayer= function (feature, latlng) {
                    return L.marker(latlng, {icon: markerround});
                }
        } else if(point.properties['opts']['icon'] === 'markerpurple'){
            opt.pointToLayer= function (feature, latlng) {
                    return L.marker(latlng, {icon: markerpurple});
                }
        }
        if(point.properties['opts']['color']){
            opt.style = function (feature) {
                return {color: feature.properties['opts']['color']}
            }
        }
        let marker =  L.geoJSON(point, opt);
        marker.bindPopup(point.properties['label']);

        switch (point.properties['category']) {
            case 'parking':
                parkings.addLayer(marker);
                break;
            case 'velo':
                velos.addLayer(marker);
                break;
            case 'transport':
                transport.addLayer(marker);
                break;
            case 'myPoints':
                myPoints.addLayer(marker);
                break;
            default:
                marker.addTo(map);

        }
    });
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


map.on('click', onMapClick);

