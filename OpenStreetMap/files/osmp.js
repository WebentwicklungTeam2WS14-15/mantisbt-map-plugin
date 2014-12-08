var osmp = Object.create(null);
osmp.map = undefined;
osmp.marker_layer = undefined;

// Icon to display an issue on the map (Base64)
// http://www.flaticon.com/free-icon/location_3913
// Icon made by Daniel Bruce (http://www.danielbruce.se) from Flaticon (www.flaticon.com) is licensed under
// CC BY 3.0 (http://creativecommons.org/licenses/by/3.0/)
// Modified with red bevel overlay and drop shadow.
osmp.marker_icon = 'data:image/png;base64,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';






/*
 * Loads the basic map.
 */
osmp.loadMap = function () {
    var osmp_container = 'osmp_map';
    var osmp_layer = 'osm';
    console.log("Map container: " + osmp_container);
    osmp.map = new ol.Map({
        target: osmp_container,
        interactions: ol.interaction.defaults({mouseWheelZoom: false}),
        layers: [
            new ol.layer.Tile({
                source: new ol.source.MapQuest({layer: osmp_layer})
            })
        ]
    });
};


/*
 * Centers the map on the given position using the specified zoom.
 */
osmp.setMapPosition = function (lng, lat, zoom) {
    var osmp_zoom = zoom;
    console.log("Setting map view: Lat=" + lat + ", Lng=" + lng + " zoom: " + osmp_zoom);
    osmp.map.setView(new ol.View({
        center: ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857'),
        zoom: osmp_zoom
    }));
};


/*
* Centers the map on the given position.
*/
osmp.setMapPositionKeepZoom = function (lng, lat) {
    var osmp_zoom = osmp.map.getView().getZoom();
    console.log("Setting map view: Lat=" + lat + ", Lng=" + lng + " zoom: " + osmp_zoom);
    osmp.map.setView(new ol.View({
        center: ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857'),
        zoom: osmp_zoom
    }));
};


/*
 * Resolves coordinates to an address.
 */
osmp.resolveCoordinates = function (lng, lat, callback) {
    console.log("Resolving coordinates: Lat=" + lat + " Lng=" + lng);
    var link = getResolveCoordinateLink(lng, lat);
    console.log("Running geocoder: " + link);
    this.webRequest(link, function (response) {
        var address = response.results[0].formatted_address;
        console.log("Retrieved address: " + address);
        callback(address);
    });
};


/*
* Resolves coordinates and sets resulting addres to the display html element.
*/
osmp.setAddressText = function (lng, lat) {
    console.log("Setting address text");
    osmp.resolveCoordinates(lng, lat, function (address) {
        document.getElementById('map_address_display_text').innerHTML = address;
    });
};


/*
 * Resolves an address and writes coordinates to the document.
 */
osmp.resolveAddress = function (address, callback) {
    console.log("Resolving address: " + address);
    var link = osmp.getResolveAddressLink(address);
    console.log("Running geocoder: " + link);
    this.webRequest(link, function (response) {
        var lat = response.results[0].geometry.location.lat;
        var lng = response.results[0].geometry.location.lng;
        console.log("Retrieved coordinates " + lat + ", " + lng);
        callback(lat, lng);
        //document.getElementById('map_coordinates_display_text').innerHTML = address;
    });
};

/*
 * Clears all markers and sets a new one to specified position.
 */
osmp.clearAndSetMarker = function (lng, lat) {
  if (osmp.marker_layer == undefined) {
    osmp.setMapPosition(lng,lat,17);
  }
    osmp.clearMarkers();
    osmp.addMarker(lng, lat);
};

/*
 * Adds a marker to the map.
 */
osmp.addMarker = function (lng, lat) {
    console.log("Showing map marker Lat=" + lat + ", Lng=" + lng);
    // Marker position
    var iconFeature = new ol.Feature({
        geometry: new ol.geom.Point(ol.proj.transform([lng, lat], 'EPSG:4326', 'EPSG:3857')),
        name: 'Ihre position'
    });
    // Marker style
    var iconStyle = new ol.style.Style({
        image: new ol.style.Icon({
            opacity: 0.90,
            anchor: [0.5, 56],
            anchorXUnits: 'fraction',
            anchorYUnits: 'pixels',
            src: osmp.marker_icon
        })
    });

    // Set icon style
    iconFeature.setStyle(iconStyle);

    // Set icon(s) to vector source
    var vectorSource = new ol.source.Vector({
        features: [iconFeature]
    });

    osmp.marker_layer = new ol.layer.Vector({
        source: vectorSource,
    });
    // Add icon layer to map
    osmp.map.addLayer(osmp.marker_layer);
};


/*
 * Clears all markers.
 */
osmp.clearMarkers = function() {
    if (osmp.marker_layer !== undefined) {
      osmp.map.removeLayer(osmp.marker_layer);
    }
};


/*
 * Builds the link to resolve an address using Google geocoder
 */
osmp.getResolveAddressLink = function (address) {
    var link = 'http://maps.googleapis.com/maps/api/geocode/json?address='
    + address
    + "&language=de";
    link = link.replace(/\s+/g, '+'); // Replace (multiple) spaces with plus char
    return link;
};


/*
 * Builds the link to resolve coordinates using Google geocoder
 */
osmp.getResolveCoordinateLink = function (lng, lat) {
    var link = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='
    + lat + ',' + lng
    + "&language=de";
    return link;
};


/*
 * Centers map on clicked position and adds a single marker
 */
osmp.setPositionClickHandler = function (){
    osmp.map.on('click', function(evt) {
        var coordinate = evt.coordinate;
        // Transfor position for further use
        var position = ol.proj.transform(coordinate, 'EPSG:3857','EPSG:4326');
        console.log("Clicked position on map. Lat=" + position[1] + ", Lng=" + position[0]);
        document.getElementById('hidden_input_latitude').value = position[1];
        document.getElementById('hidden_input_longitude').value = position[0];
        osmp.clearAndSetMarker(position[0], position[1]);
        var link = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + position[1] + ',' + position[0] + "&language=de";
        link = link.replace(/\s+/g, '+');
        osmp.webRequest(link, function (response){
            var address = response.results[0].formatted_address;
            console.log("Setting address: " + address);
            document.getElementById('map_address_input').value = address;
            document.getElementById('hidden_input_address').value = address;
        });
    });
 };

/*
 * Activates Google autocomplete for the 'map_address_input' text input.
 */
osmp.setGoogleAutocomplete = function(){
   console.log("Setting autocomplete");
   var autocomplete = new google.maps.places.Autocomplete(
        (document.getElementById('map_address_input')),
        { types: ['geocode'] });
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        var place = autocomplete.getPlace();
        var address = place.formatted_address;
        var lat = place.geometry.location.lat();
        var lng = place.geometry.location.lng();
        document.getElementById('hidden_input_latitude').value = lat;
        document.getElementById('hidden_input_longitude').value = lng;
        document.getElementById('hidden_input_address').value = address;
        osmp.setMapPosition(lng,lat,17);
        osmp.clearAndSetMarker(lng, lat);
    });
};

/*
 * Attempt to prevent hitting enter completing the bug report.
 */
osmp.catchEnter = function (event) {
    if (event.keyCode == 13){
        console.log("Enter was pressed");
        return false;
    }
};


/*
 * Requests a document from a given url.
 */
osmp.webRequest = function (url, callback) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var result = JSON.parse(xmlhttp.responseText);
            if(callback){
                callback(result);
            }
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
};
