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
    <style type="text/css">
      html,body { height: 98%; margin: 0px; padding: 6px; }
      #map { height: 100% }
	  .aligncenter { text-align: center; }
	  .custom-popup .leaflet-popup-content-wrapper { font-size: 100%; }
	</style>
    <script type="text/javascript">
</script>
  </head>
  <body>

    <div id="map"></div>
    <script type="text/javascript">
    var result = L.latLng(0, 0);

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
    }

    try {
    	getGeoIP();
    }
    catch(err) {
    	console.log("GeoIP JS blocked by client or adblock");
    	result.lat = 37.75;
    	result.lng = -122.42;
    }

	var map = L.map('map').setView([result.lat, result.lng], 13);

	var osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>' }).addTo(map);

	var satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 20, attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community' });

	map.locate({setView: true, watch: false, maxZoom:18})
		.on('locationfound', function(e){
            var marker = L.marker([e.latitude, e.longitude]).bindPopup('Current location');
            map.addLayer(marker);
        })
       .on('locationerror', function(e){
            alert("Location access denied.");
        });

	var FruitIcon = L.Icon.extend({
    		options: {
        iconSize:     [25, 25],
        iconAnchor:   [25, 25],
        popupAnchor:  [-12, -26]
    		}
	});

	var icons = {
		orange: new FruitIcon({iconUrl: 'images/orange.png'}),
		apple: new FruitIcon({iconUrl: 'images/apple.png'}),
		lemon: new FruitIcon({iconUrl: 'images/lemon.png'}),
		lime: new FruitIcon({iconUrl: 'images/lime.png'}),
		blackberry: new FruitIcon({iconUrl: 'images/blackberry.png'}),
		avocado: new FruitIcon({iconUrl: 'images/avocado.png'}),
		plum: new FruitIcon({iconUrl: 'images/plum.png'}),
		peach: new FruitIcon({iconUrl: 'images/peach.png'}),
		fig: new FruitIcon({iconUrl: 'images/fig.png'}),
		mandarin: new FruitIcon({iconUrl: 'images/mandarin.png'}),
		apricot: new FruitIcon({iconUrl: 'images/apricot.png'}),
		banana: new FruitIcon({iconUrl: 'images/banana.png'}),
		blueberry: new FruitIcon({iconUrl: 'images/blueberry.png'}),
		cherry: new FruitIcon({iconUrl: 'images/cherry.png'}),
		grape: new FruitIcon({iconUrl: 'images/grape.png'}),
		grapefruit: new FruitIcon({iconUrl: 'images/grapefruit.png'}),
		guava: new FruitIcon({iconUrl: 'images/guava.png'}),
		kiwi: new FruitIcon({iconUrl: 'images/kiwi.png'}),
		mango: new FruitIcon({iconUrl: 'images/mango.png'}),
		olive: new FruitIcon({iconUrl: 'images/olive.png'}),
		pear: new FruitIcon({iconUrl: 'images/pear.png'}),
		raspberry: new FruitIcon({iconUrl: 'images/raspberry.png'}),
		strawberry: new FruitIcon({iconUrl: 'images/strawberry.png'}),
		pomegranate: new FruitIcon({iconUrl: 'images/pomegranate.png'})	
	};

	var markerFruitIcon;
	var markerLabel;
	var markerName;
	var markerIcon;
	var markerAddress;
	var locationArray = <?php echo $mapped_markers; ?>;
	var layerGroups = {};
	var fruitList = [];
	var layerTest = L.layerGroup();
	var fruit_group;

	
	// Loop through and add all locations to the map, one by one
	addMarkerArray(locationArray);

	var baseMaps = {
	    "Base": osm,
	    "Satellite": satellite
	};

	var layerControl = L.control.layers(baseMaps, layerGroups).addTo(map);

	var newMarker;
	var latLng;	
	var markerLat;
	var markerLng;
	var select_statement = '<select id="type">';


	// Prepare selct statement
	for (const [key, value] of Object.entries(icons)) {
		select_statement += "<option>" + key + "</option>";
	}	

	select_statement += '</select>';

	function addMarkerArray(markerArray) {
		for (var i = 0; i < markerArray.length; i++) {
			fruit_group = markerArray[i]['type'];				
			switch(markerArray[i]['type']) {
				case "orange":
					markerFruitIcon = icons['orange'];
					break;
				case "apple":
						markerFruitIcon = icons['apple'];
						break;
				case "lemon":
						markerFruitIcon = icons['lemon'];
						break;
				case "lime":
						markerFruitIcon = icons['lime'];
						break;
				case "blackberry":
						markerFruitIcon = icons['blackberry'];
						break;
				case "avocado":
						markerFruitIcon = icons['avocado'];
						break;
				case "plum":
						markerFruitIcon = icons['plum'];
						break;
				case "peach":
						markerFruitIcon = icons['peach'];
						break;
				case "fig":
						markerFruitIcon = icons['fig'];
						break;
				case "mandarin":
						markerFruitIcon = icons['mandarin'];
						break;
				case "apricot":
						markerFruitIcon = icons['apricot'];
						break;
				case "banana":
						markerFruitIcon = icons['banana'];
						break;
				case "blueberry":
						markerFruitIcon = icons['blueberry'];
						break;
				case "cherry":
						markerFruitIcon = icons['cherry'];
						break;
				case "grape":
						markerFruitIcon = icons['grape'];
						break;
				case "grapefruit":
						markerFruitIcon = icons['grapefruit'];
						break;
				case "guava":
						markerFruitIcon = icons['guava'];
						break;
				case "kiwi":
						markerFruitIcon = icons['kiwi'];
						break;
				case "mango":
						markerFruitIcon = icons['mango'];
						break;
				case "olive":
						markerFruitIcon = icons['olive'];
						break;
				case "pear":
						markerFruitIcon = icons['pear'];
						break;
				case "raspberry":
						markerFruitIcon = icons['raspberry'];
						break;
				case "strawberry":
						markerFruitIcon = icons['strawberry'];
						break;
				case "pomegranate":
						markerFruitIcon = icons['pomegranate'];
						break;
				default:
					markerFruitIcon = icons['apple'];
			}

			if(markerArray[i]['address'] === "") {
				markerAddress = "";
			} else {
				markerAddress = "<br><b>Address:</b> " + markerArray[i]['address'];
			}

			if(markerArray[i]['name'] === "") {
				markerName = "<br>";
			} else {
				markerName = "<br><b>Name:</b> " + markerArray[i]['name'] + "<br>";
			}

			markerIcon = "<div class=\"aligncenter\"><img src=\"images/" + markerArray[i]['type'] + ".png\" width=\"32\" height=\"32\"><br></div>";

			markerLabel = markerIcon + markerName + "<b>Fruit:</b> " + markerArray[i]['type'] + "<br><b>Location:</b> " + markerArray[i]['lat'] + ", " + markerArray[i]['lng'] + markerAddress;


			try {
				layerGroups[fruit_group].addLayer(L.marker([markerArray[i].lat, markerArray[i].lng],{icon: markerFruitIcon}).bindPopup(markerLabel));
			} catch(err) {
				fruitList.push(fruit_group);
				layerGroups[fruit_group] = L.layerGroup();
				layerGroups[fruit_group].addLayer(L.marker([markerArray[i].lat, markerArray[i].lng],{icon: markerFruitIcon}).bindPopup(markerLabel)).addTo(map);
			}
		}
	}

	map.on('click', addNewMarker);

	function addNewMarker(e){
		var customOptions =
	    {
	    'keepInView' : true,
	    'className' : 'custom-popup',
	    'closeButton' : false
	    }

		var popupContent = "<table><tr><td>Fruit details*</td><td><input type='text' id='name'/></td></tr><tr><td>Location details*</td><td><input type='text' id='address'/></td></tr><tr><td>Type</td><td>" + select_statement + "</td><tr><td>* = optional</td></tr><tr><td><input type='button' value='Save & Close' onclick='saveData()'/></td><td align='right'><input type='button' value='Delete' onclick='deleteData()'/></td></tr></table>";

		newMarker = new L.marker(e.latlng).addTo(map).bindPopup(popupContent,customOptions).openPopup();
		latLng = e.latlng;
		markerLat = latLng.lat;
		markerLng = latLng.lng;
		map.off('click',addNewMarker);		
	};

	function doNothing() {};

	function downloadUrl(url, callback) {
		var request = window.ActiveXObject ?
		new ActiveXObject('Microsoft.XMLHTTP') :
		new XMLHttpRequest;
		request.onreadystatechange = function() {
	  	if (request.readyState == 4) {
	    		request.onreadystatechange = doNothing;
	    		callback(request, request.status);
	  	}
		};
		request.open('GET', url, true);
		request.send(null);
	}  

	function saveData() {
		var name = escape(document.getElementById("name").value);
		var address = escape(document.getElementById("address").value);
		var type = document.getElementById("type").value;

//		if (name !== "" && address !== "" && type !== "") {
		if (type !== "") {

			map.closePopup();
			var url = "addrecord.php?name=" + name + "&address=" + address +
			"&type=" + type + "&lat=" + markerLat + "&lng=" + markerLng;	
			downloadUrl(url, function(data, responseCode) {
				if (responseCode == 200) {
					window.location.reload();
				}
				else {
//					console.log("failed to submit");
				}
			});
		}
		else {
			window.alert("Missing value");
		}

    }

	function deleteData() {
		map.removeLayer(newMarker);
		map.closePopup();
		map.on('click', addNewMarker);
	}

    </script>
  
  </body>
</html>
