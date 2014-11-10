var osmp = new Object;
osmp.showMap = function() {
  var map = new ol.Map({
    target: 'osmp_map',
    layers: [
      new ol.layer.Tile({
        source: new ol.source.MapQuest({layer: 'osm'})
      })
    ],
    view: new ol.View({
      center: ol.proj.transform([51.499778, 6.545100], 'EPSG:4326', 'EPSG:3857'),
      zoom: 5
    })
  });
}
