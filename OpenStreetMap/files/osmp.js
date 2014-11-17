var osmp = new Object();
osmp.map = undefined;

osmp.showMap = function (lng, lat) {
  this.loadMap();
  this.setMapPosition(lng, lat);
}

osmp.loadMap = function (){
  var osmp_container = 'osmp_map';
  var osmp_layer = 'osm';

  console.log("Map container: " + osmp_container);
  osmp.map = new ol.Map({
    target: osmp_container,
    layers: [
      new ol.layer.Tile({
        source: new ol.source.MapQuest({layer: osmp_layer})
      })
    ]
  });
}

osmp.setMapPosition = function (lng, lat){
  var proj_from = new ol.proj.Projection('EPSG:4326'); // transform from WGS 1984
  var proj_to   = new ol.proj.Projection('EPSG:3857'); // to projected crs


  var osmp_zoom = 17;
  console.log("Setting map view: Lat=" + lat + ", Lng=" + lng + " zoom: " + osmp_zoom);
  osmp.map.setView(new ol.View({
    center: ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857'),
    zoom: osmp_zoom
  }));
}

osmp.getAddress = function (lng, lat){
  console.log("Resolving coordinates: Lat=" + lat + ", Lng=" + lng);
  var link = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + lat + ',' + lng+ "&language=de";
  console.log(link);
  var address = 'Alleestraße 28, 46282 Dorsten';
  console.log("Retrieved address: " + address);
  document.write(address);
}

osmp.getCoordinates = function (address){
  console.log("Resolving address: " + address);
  var link = 'http://maps.googleapis.com/maps/api/geocode/json?address=' + address + "&language=de";
  link = link.replace(/\s+/g, '+'); // Replace (multiple) spaces with plus char
  console.log(link);
  console.log("Retrieved coordinates")
}

osmp.jsonRequest = function (url, callback) {
  var xmlhttp = new XMLHttpRequest();

  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        var myArr = JSON.parse(xmlhttp.responseText);
        callback(myArr);
    }
  }
  xmlhttp.open("GET", url, true);
  xmlhttp.send();
}

osmp.readAddressResponse = function (response){
  var address = response.results[0].formatted_address;
  console.log(address);
  document.write(address);
}

osmp.readCoordinateResponse = function (response){
  var lat;
  var lng;
}
