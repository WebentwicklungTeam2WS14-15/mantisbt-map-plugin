var osmp = new Object;
osmp.map;

osmp.showMap = function(lat, lng) {
  this.loadMap();
  this.setMapPosition(lat, lng);
}

osmp.loadMap = function(){
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

osmp.setMapPosition = function(lat, lng){
  var proj_from = new ol.proj.Projection('EPSG:4326'); // transform from WGS 1984
  var proj_to   = new ol.proj.Projection('EPSG:3857'); // to projected crs


  var osmp_zoom = 17;
  console.log("Setting map view: Lat=" + lat + ", Lng=" + lng + " zoom: " + osmp_zoom);
  osmp.map.setView(new ol.View({
    center: ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857'),
    zoom: osmp_zoom
  }));
}
