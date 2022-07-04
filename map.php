<?php
require_once("locations.php");
?>

<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>FrootShare | Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin=""></script>
<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
   <style type="text/css">
	.custom-popup .leaflet-popup-content-wrapper, .custom-popup .leaflet-popup-tip, .custom-popup .custom-table {
		background-color: #92a8d1;
		font-size: 12px;
	}
	.custom-img {text-align: center}
	.custom-button {
		height: 20px; 
		width: 85px;
		font-size: 10px;
		margin: 2px
	}
	.custom-input {
		width: 100px;
		height: 20px;
		font-size: 12px
	}
	.custom-select {
		width: 100px;
		height: 20px;
		font-size: 12px
	}
	.leaflet-bar i, .leaflet-bar span { line-height:30px; }
	</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
<script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>

  </head>
  <body>
    <div id="map" style="height: 100%; width: 100%"></div>
    <script type="text/javascript">
	// Set up initial global variables
	var locationArray = <?php echo $mapped_markers; ?>;
	var result = L.latLng(0, 0);
	var loopGroup = new L.layerGroup();
	var fruitTempArray = [];
	var fruitLayerGroups = {};
	var fruitIcons = {};
	var select_statement = '<select id="type" class="custom-select">';
	var newMarker;
	var initialMarker;
	var initialZoom;
	var customOptions = {'keepInView' : true, 'className' : 'custom-popup', 'closeButton' : true};

	// Prepare data for loops
	// Read markers for fruit names
	for (var i = 0; i < locationArray.length; i++) {
		markerGroup = locationArray[i]['type'];
		fruitTempArray.push(markerGroup);
	}

	// Remove duplicates from fruit name array before creating layers
	var fruitNameArray = [new Set(fruitTempArray)][0];

	// Start of map section
	// Get GeoIP for initial map load
	function getGeoIP() {
		var url = "https://json.geoiplookup.io/";
		var xhr = new XMLHttpRequest();
		xhr.open("GET", url, false);
		xhr.onload = function () {
		    var status = xhr.status;
		    if (status == 200) {
			var geoip_response = JSON.parse(xhr.responseText);
			result.lat = geoip_response.latitude;
			result.lng = geoip_response.longitude;
		    } else {
			console.log("Leaflet.GeoLocation.getGeoIPPosition failed because its XMLHttpRequest got this response: " + xhr.status);
		    }
		};
		xhr.send();
		return result;
	};

	// Try to get GeoIP, otherwise just set default to San Francisco
	try {
		getGeoIP();
	}
	catch(err) {
		console.log("GeoIP JS blocked by client or adblock");
		result.lat = 37.75;
		result.lng = -122.42;
	};

	// Load initial map view
	var map = L.map('map').setView([result.lat, result.lng], 13);

	// Set up tile layers, only 2 added, base from OSM and satellite from ESRI
	var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>' }).addTo(map);
	var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 20, attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community' });


	// Check for user location, add to map or show error
	map.locate({setView: true, watch: false, maxZoom:18}).on('locationfound', function(e){
            initialMarker = L.marker([e.latitude, e.longitude]).bindPopup('Current location', customOptions);
	    map.addLayer(initialMarker);
	    initialZoom = map.getZoom();
	    result.lat = e.latitude;
	    result.lng = e.longitude;
        }).on('locationerror', function(e){
            alert("Location access denied.");
        });


	// Add easy buttons to map for custom functions
	L.easyButton( 'fa-ban', function() { turnLayerOff()},"Hide icons").addTo(map);
	L.easyButton( 'fa-list', function() { turnLayerOn()},"Show icons").addTo(map);
	L.easyButton( 'fa-location-arrow', function() { map.setView([result.lat, result.lng],initialZoom)},"Re-center map").addTo(map);

	// Create layer groups in fruitLayerGroup object from fruitNameArray
	fruitNameArray.forEach(addFruitLayer);
	function addFruitLayer(item) {
		fruitLayerGroups[item] = new L.layerGroup().addTo(map);
		fruitIcons[item] = new L.icon({iconUrl: "images/" + item + ".png", iconSize: [32, 32]});
		select_statement += "<option>" + item + "</option>";
	}
	select_statement += '</select>';


	// Add existing markers from database
	// Loop through and add all existing locations to the corresponding fruit layer from the array, one by one
	addMarkerArray(locationArray);

	// Prepare base layer object
	var baseMaps = {
	    "Base": osm,
	    "Satellite": satellite
	};

	// Set and enable Layer control UI menu options
	var layerControl = L.control.layers(baseMaps,fruitLayerGroups).addTo(map);

	// Function to call if a new location is clicked, adds marker to map temporarily while entering in values
	// Call function for when user clicks anywhere on map
	map.on('click', addNewMarker);

	// Function that is called to load existing markers
	function addMarkerArray(markerArray) {
		for (var i = 0; i < markerArray.length; i++) {
			markerId = markerArray[i]['id'];
			markerName = markerArray[i]['name'];
			markerAddress = markerArray[i]['address'];
			markerLat = markerArray[i]['lat'];
			markerLng = markerArray[i]['lng'];
			markerGroup = markerArray[i]['type'];

			// Check and replace missing values from db records -- not used
			if(markerAddress === "") {
				markerLabelAddress = "";
			} else {
				markerLabelAddress = "<br><b>Address:</b> " + markerArray[i]['address'];
			}

			if(markerName === "") {
				markerLabelName = "<br>";
			} else {
				markerLabelName = "<br><b>Name:</b> " + markerName + "<br>";
			}

			markerLabelIcon = "<div class='custom-img'><img src=\"images/" + markerGroup + ".png\" width='32' height='32'><br></div>";
			markerPopupContent = markerLabelIcon + "<b>Fruit:</b> " + markerGroup + "<br><b>Location:</b> " + markerLat + ", " + markerLng;

			var loopMarker = new L.Marker([markerLat, markerLng],{icon: fruitIcons[markerGroup]}).bindPopup(markerPopupContent, customOptions);
			fruitLayerGroups[markerGroup].addLayer(loopMarker);
		}
	}

	// Function called when a new marked is added by a click
	function addNewMarker(e){
		var customOptions =
		    {
		    'keepInView' : true,
		    'className' : 'custom-popup',
		    'closeButton' : true
		    }
		var latLng = e.latlng;
		var markerLat = latLng.lat;
		var markerLng = latLng.lng;

		var popupContent = "<table class='custom-table'><tr><td><b>Fruit*</b></td><td>" + select_statement + "</td></tr><tr><td>Name</td><td><input type='text' id='name' class='custom-input'></td></tr><tr><td>Description</td><td><input type='text' id='address' class='custom-input'></td></tr><tr><td><b>* Required</b></td></tr></table><input type='button' class='custom-button' value='Save & Close' onclick='saveData()'/><input type='button' class='custom-button' value='Delete' onclick='deleteData()'/>";

		newMarker = new L.marker(e.latlng).addTo(map).bindPopup(popupContent,customOptions).openPopup();

		// Disable any new locations from being added
		map.off('click',addNewMarker);		
	};

	// Function called when the form is saved to submit the new marker
	function saveData() {
		var name = escape(document.getElementById("name").value);
		var address = escape(document.getElementById("address").value);
		var type = document.getElementById("type").value;

		if (type !== "") {
			map.closePopup();
			var url = "addrecord.php?name=" + name + "&address=" + address + "&type=" + type + "&lat=" + markerLat + "&lng=" + markerLng;	
			downloadUrl(url, function(data, responseCode) {
				if (responseCode == 200) {
					window.location.reload();
				}
				else {
					console.log("failed to submit");
				}
			});
		}
		else {
			window.alert("Missing value");
		}
	}

	// Function called if the delete marker button is clicked on the form
	function deleteData() {
		map.removeLayer(newMarker);
		map.closePopup();
		map.on('click', addNewMarker);
	}

	// Added to handle request.onreadystatechange when ready, we don't need response content, just confirm 200
	function doNothing() {};

	// Function called to submit the form to the server and reload the page with the new data
	function downloadUrl(url, callback) {
		var request = window.ActiveXObject ?
		new ActiveXObject('Microsoft.XMLHTTP') :
		new XMLHttpRequest;
		request.onreadystatechange = function() {
		if (request.readyState == 4) {
			// We don't need response content, so just call doNothing once data is confirmed 200
			request.onreadystatechange = doNothing;
			callback(request, request.status);
		}
		};
		request.open('GET', url, true);
		request.send(null);
	}  

	// Function called when turn off all layers button is clicked
	function turnLayerOff() {
		fruitNameArray.forEach(removeMarker);
		function removeMarker(item) {
			if(map.hasLayer(fruitLayerGroups[item])) {
				map.removeLayer(fruitLayerGroups[item]);
			}
		};
	};

	// Function called when turn on all layers button is clicked
	function turnLayerOn() {
		fruitNameArray.forEach(addMarkerBack);
		function addMarkerBack(item) {
			if(!map.hasLayer(fruitLayerGroups[item])) {
				map.addLayer(fruitLayerGroups[item]);
			}
		};
	};
    </script>
  
  </body>
</html>
