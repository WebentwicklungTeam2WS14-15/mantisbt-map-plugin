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
  if (osmp.marker_layer === undefined) {
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

osmp.checkBounds = function(lng, lat) {

};


/*
 * Builds the link to resolve an address using Google geocoder
 */
osmp.getResolveAddressLink = function (address) {
	var link = 'http://maps.googleapis.com/maps/api/geocode/json?address=' + address + "&language=de";
	link = link.replace(/\s+/g, '+'); // Replace (multiple) spaces with plus char
	return link;
};


/*
 * Builds the link to resolve coordinates using Google geocoder
 */
osmp.getResolveCoordinateLink = function (lng, lat) {
	var link = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' + lat + ',' + lng + "&language=de";
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
			document.getElementsByName("custom_field_3")[0].value = address;
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
		document.getElementsByName("custom_field_3")[0].value = address;
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
