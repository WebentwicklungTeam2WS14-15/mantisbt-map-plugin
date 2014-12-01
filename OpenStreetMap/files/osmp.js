var osmp = new Object();
osmp.map = undefined;
osmp.marker_layer = undefined;

// Icon to display an issue on the map
// Copyright Map Icons Collection Creative Commons 3.0 BY-SA Author : Nicolas Mollet
osmp.marker_icon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAlCAYAAAAjt+tHAAACnUlEQVRYhbWXu24TQR'
+ 'SGv1ksa21CiihConJewEKIIrIo0th9niFW8hZUvAUInsH9VimQlSJCiBeAKo1FEYyzspYZCs8xx85eZs36SKO9er5/zvl3vcc45'
+ 'wAYjUYtIALaftui2cgACywBmyRJBmCccwJvA7EfbS8gaghuvYAlkPqxTJIkM8PhUOBd4NBvY/aTgRRYAPd+u5RVxh7+3Dk3b'
+ 'Ri8EcaYgd+1QKZT33XOTc8fUl5Yy7F1jYJnkeEuiph04qkxpo8vgzZeDOwFDvg5rRyKzyJxu4wg+NnNtPS4XARopri8ltuvTw'
+ 'eFUH2+QlhUG1wGk+Pr08Gj/UoVu4QGVQlrTEBvfLEByFvh9rUqMcECeuMLTq4uH4HywHXKECRA4DKZnjRv/+xmGlyGS'
+ 'gEaHvL4FWViJwFl8LzJ8+pfVYZCASEr16ssErazBwQuzhdY2YqrztcSEPJjDdsFDjX+8yUTRZPr7ITCawkoewdsR+hrGGq8iLT'
+ 'h8swn57UInbX/FhAaWsTJ1WVzr2IdvfFF6eokQ9/ffwDKS7bTh6f2Q1n8+PhpfX+RL8xwODwCjoEj59z07a/fu2gKjnfPnsqH'
+ '6U9gJiWwJb/ZV1hYeSBTg1lk9kZUc6+ZEf/apRTgLor2IkI+y32knmlbCr4wxgwmnTjXsucPKf3sTxDsW+sJk06ce83Xf4HqC'
+ '6w/uPc39NlszSKASSf+TIAIgRtj3vhT4q/t1iwFbEhzuv6Gd87dlmVCwV+z6a3i5rSiPZetbt++5IlQ8Fc6xVsC8tvzsvDCYuDAj'
+ '65z7qsWoeAvPXzuRyqgoqgUUCDiQDIB6JXPqQEPFpAj4tCLuAWk5nNW5gqG1xKwJaLrR9tfWrJK/aIOvLYAJUJMKY+qPGLL'
+ 'OnCAv1nXlMCzka/IAAAAAElFTkSuQmCC';

/*
 * Loads the map and centers on the given position.
 */
osmp.showMap = function (lng, lat) {
  this.loadMap();
  this.setMapPosition(lng, lat);
  this.showMarker(lng, lat);
  this.setClickPositionHandler();
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
    interactions: ol.interaction.defaults({mouseWheelZoom:false}),
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
osmp.setMapPosition = function (lng, lat, zoom){
  var osmp_zoom = zoom;
  console.log("Setting map view: Lat=" + lat + ", Lng=" + lng + " zoom: " + osmp_zoom);
  osmp.map.setView(new ol.View({
    center: ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857'),
    zoom: osmp_zoom
  }));
}

osmp.setMapClickPosition = function (lng, lat){
  var osmp_zoom = osmp.map.getView().getZoom();
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
    var lat = response.results[0].geometry.location.lat;
    var lng = response.results[0].geometry.location.lng;
    console.log("Retrieved coordinates " + lat + ", " + lng);
    //document.getElementById('map_coordinates_display_text').innerHTML = address;
  });
}

/*
 * Adds a marker to the map.
 */
osmp.showMarker = function(lng, lat){
  console.log("Showing map marker Lat=" + lat + ", Lng=" + lng);
  // Marker position
  var iconFeature = new ol.Feature({
    geometry: new ol.geom.Point(ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857')),
    name: 'Ihre position',
  });
  // Marker style
  var iconStyle = new ol.style.Style({
    image: new ol.style.Icon({
      opacity: 0.90,
      anchor: [0.5, 46],
      anchorXUnits: 'fraction',
      anchorYUnits: 'pixels',
      graphicWidth:50,
      graphicHeight:50,
      src: osmp.marker_icon
    })
  });
  // Set icon style
  iconFeature.setStyle(iconStyle);

  // Set icon(s) to vector source
  var vectorSource = new ol.source.Vector({
    features: [iconFeature]
  });
  if(osmp.marker_layer != undefined){
    osmp.map.removeLayer(osmp.marker_layer);
  }
  osmp.marker_layer = new ol.layer.Vector({
    source: vectorSource,
  });
  // Add icon layer to map
  osmp.map.addLayer(osmp.marker_layer);
}


 osmp.setClickPositionHandler = function (){
   osmp.map.on('click', function(evt) {
     var coordinate = evt.coordinate;
     // Transfor position for further use
     var position = ol.proj.transform(coordinate, 'EPSG:3857','EPSG:4326');
     console.log("Registered click on map. Selected position: " + position[0] + "," + position[1]);
     osmp.setMapClickPosition(position[0],position[1]);
     osmp.showMarker(position[0], position[1]);
     var link = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + position[1] + ',' + position[0]+ "&language=de";
     link = link.replace(/\s+/g, '+');
     osmp.jsonRequest(link, function (response){
       var address = response.results[0].formatted_address;
       document.getElementById('map_address_input').value = address;
     });
   });
 }

 osmp.updateMapFromInput = function(){
   var text  = document.getElementById('map_address_input').value;
   this.getCoordinates(text);
   console.log("text has changed " + text);
 }
 osmp.setGoogleAutocomplete = function(){
   console.log("Setting autocomplete");
   var autocomplete = new google.maps.places.Autocomplete(
     /** @type {HTMLInputElement} */(document.getElementById('map_address_input')),
     { types: ['geocode'] });
     google.maps.event.addListener(autocomplete, 'place_changed', function() {
       var place = autocomplete.getPlace();
       var address = place.formatted_address;
       var lat = place.geometry.location.lat();
       var lng = place.geometry.location.lng();
       osmp.setMapPosition(lng,lat,17);
       osmp.showMarker(lng, lat);
     });
 }

 osmp.catchEnter = function(event){
   if (event.keyCode == 13){
     console.log("Enter was pressed");
     return false;
   }
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
