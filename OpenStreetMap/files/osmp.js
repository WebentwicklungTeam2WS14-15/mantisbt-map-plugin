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
  var proj_to   = new ol.proj.Projection('EPSG:900913'); // to Spherical Mercator Projection
  //var center = new ol.LonLat(lng, lat);
  //center.transform(coor_from, coor_to);
  //osmp.map.setCenter(center, 12);

  var osmp_zoom = 5;
  console.log("Setting map view to: " + lat + ", " + lng + " zoom: " + osmp_zoom);
  osmp.map.setView(new ol.View({
    center: ol.proj.transform([lat, lng], proj_from, proj_to),
    zoom: osmp_zoom
  }));

}
