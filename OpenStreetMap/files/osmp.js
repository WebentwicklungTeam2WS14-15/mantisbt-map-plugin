var osmp = new Object();
osmp.map = undefined;

/*
 * Loads the map and centers on the given position.
 */
osmp.showMap = function (lng, lat) {
  this.loadMap();
  this.setMapPosition(lng, lat);
  this.showMarker(lng, lat);
}

/*
 * Loads basic map functionality.
 */
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

/*
 * Centers the map on the given position.
 */
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

/*
 * Resolves coordinates and writes address to the document.
 */
osmp.getAddress = function (lng, lat){
  console.log("Resolving coordinates: Lat=" + lat + " Lng=" + lng);
  var link = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + lat + ',' + lng+ "&language=de";
  console.log("Running geocoder: " + link);
  this.jsonRequest(link, function (response){
    var address = response.results[0].formatted_address;
    console.log("Retrieved address: " + address);
    document.getElementById('map_address_display_text').innerHTML = address;
  });
}

/*
 * Resolves an address and writes coordinates to the document.
 */
osmp.getCoordinates = function (address){
  console.log("Resolving address: " + address);
  var link = 'http://maps.googleapis.com/maps/api/geocode/json?address=' + address + "&language=de";
  link = link.replace(/\s+/g, '+'); // Replace (multiple) spaces with plus char
  console.log("Running geocoder: " + link);
  this.jsonRequest(link, function(response){
    console.log("Retrieved coordinates" + response);
    //document.getElementById('map_coordinates_display_text').innerHTML = address;
  });
}

/*
 * Adds a marker to the map.
 */
osmp.showMarker = function(lng, lat){
  var markers = new ol.Layer.Markers("Markers");
  osmp.map.addLayer(markers);
  var size = new ol.Size(21,25);
  var offset = new ol.Pixel(-(size.w/2), -size.h);
  var icon = new ol.Icon('http://www.openlayers.org/dev/img/marker.png',size,offset);
  markers.addMarker(new ol.Marker(new ol.LonLat(lng, lat), icon));
}

/*
 * Requests a document from a given address.
 */
osmp.jsonRequest = function (url, callback) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
        var result = JSON.parse(xmlhttp.responseText);
        if(callback){
          callback(result);
        }
    }
  }
  xmlhttp.open("GET", url, true);
  xmlhttp.send();
}
