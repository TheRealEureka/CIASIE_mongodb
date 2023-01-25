import togglePanel from "./popup.js";
let points = [];
let addMode = false;
const mapObject = document.getElementById('map');



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
    layers: [baselayer, myPoints]
}).setView([48.6880561, 6.1559293], 13);

fetch('/api/getdata'
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
        points.push(point)
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
    displayPointsList()
});

function onMapClick(e) {
    if(addMode) {
        let marker = L.marker([e.latlng.lat, e.latlng.lng], {icon: markerpurple});
        myPoints.addLayer(marker);
        let label = prompt("Label");
        if (label) {
            marker.bindPopup(label);
        }
        points.push(marker);
        addMode = false;
        mapObject.classList.remove('cible');

    }
}

function displayPointsList(){
   let pointsData = {
       velos : points.filter(point => point.properties['category'] === 'velo'),
       parkings : points.filter(point => point.properties['category'] === 'parking'),
       transports : points.filter(point => {return point.properties['category'] === 'transport' && point.geometry['type'] === 'Point'}),
       myPoints : points.filter(point => point.properties['category'] === 'myPoints'),
   }
   let veloList = document.getElementById('veloList');
    let parkingList = document.getElementById('parkingList');
    let transportList = document.getElementById('transportList');
    let myPointsList = document.getElementById('myPointsList');
    veloList.innerHTML = '';
    parkingList.innerHTML = '';
    transportList.innerHTML = '';
    myPointsList.innerHTML = '';

    veloList.append(pointsToHTML(pointsData.velos));
    parkingList.append(pointsToHTML(pointsData.parkings));
    transportList.append(pointsToHTML(pointsData.transports));
    myPointsList.append(pointsToHTML(pointsData.myPoints))
}
function pointsToHTML(points){

    let ul = document.createElement('ul');
    ul.classList.add('list-group');
    ul.append(...points.map(pointToHTML))
    return ul;
}
function pointToHTML(point){
   let li = document.createElement('li');
    li.classList.add('list-group-item');
    li.innerHTML = point.properties['name'];
    li.addEventListener('click', () => {
        map.flyTo([point.geometry.coordinates[1], point.geometry.coordinates[0]], 15);
        console.log(point);
    });
    return li;
}


let overlayMaps = {
    "Parkings": parkings,
    "Stations de v\u00e9los": velos,
    "R\u00e9seau STAN": transport,
    "Mes points": myPoints
};


L.control.layers({}, overlayMaps).addTo(map);

document.getElementById("newpoint").addEventListener("click", toggleAddMode);
function toggleAddMode() {
    addMode=!addMode;
    togglePanel();
    if(addMode)
    {
        mapObject.classList.add('cible');
    }else{
        mapObject.classList.remove('cible');
    }
}
map.on('click', onMapClick);

