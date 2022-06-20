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
    </style>
    <script type="text/javascript">
</script>
  </head>
  <body>
    <div id="map"></div>
<!--    <button id="mapfruit" onclick="window.location.href='leaflet.php'">Map</button>
-->

    <script type="text/javascript">
	var map = L.map('map').setView([51.505, -0.09], 13);

	L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	    maxZoom: 19,
	    attribution: '© OpenStreetMap'
	}).addTo(map);
	
    	map.locate({setView: true, watch: false, maxZoom:18}).on('locationfound', function(e){
            var marker = L.marker([e.latitude, e.longitude]).bindPopup('Current location');
            map.addLayer(marker);
        })
       .on('locationerror', function(e){
 //           console.log(e);
            alert("Location access denied.");
        });

	var FruitIcon = L.Icon.extend({
    		options: {
        iconSize:     [25, 25],
        iconAnchor:   [25, 25],
        popupAnchor:  [-12, -26]
    		}
	});

//        var orange = new FruitIcon({iconUrl: 'images/orange-icon.png'});

	var icons = {
		orange: new FruitIcon({iconUrl: 'images/orange-icon.png'}),
		apple: new FruitIcon({iconUrl: 'images/apple-icon.png'}),
		lemon: new FruitIcon({iconUrl: 'images/lemon-icon.png'}),
		lime: new FruitIcon({iconUrl: 'images/lime-icon.png'}),
		blackberry: new FruitIcon({iconUrl: 'images/blackberry-icon.png'}),
		avocado: new FruitIcon({iconUrl: 'images/avocado-icon.png'}),
		plum: new FruitIcon({iconUrl: 'images/plum-icon.png'}),
		peach: new FruitIcon({iconUrl: 'images/peach-icon.png'}),
		fig: new FruitIcon({iconUrl: 'images/fig-icon.png'}),
		mandarin: new FruitIcon({iconUrl: 'images/mandarin-icon.png'}),
		apricot: new FruitIcon({iconUrl: 'images/apricot-icon.png'}),
		banana: new FruitIcon({iconUrl: 'images/banana-icon.png'}),
		blueberry: new FruitIcon({iconUrl: 'images/blueberry-icon.png'}),
		cherry: new FruitIcon({iconUrl: 'images/cherry-icon.png'}),
		grape: new FruitIcon({iconUrl: 'images/grape-icon.png'}),
		grapefruit: new FruitIcon({iconUrl: 'images/grapefruit-icon.png'}),
		guava: new FruitIcon({iconUrl: 'images/guava-icon.png'}),
		kiwi: new FruitIcon({iconUrl: 'images/kiwi-icon.png'}),
		mango: new FruitIcon({iconUrl: 'images/mango-icon.png'}),
		olive: new FruitIcon({iconUrl: 'images/olive-icon.png'}),
		pear: new FruitIcon({iconUrl: 'images/pear-icon.png'}),
		raspberry: new FruitIcon({iconUrl: 'images/raspberry-icon.png'}),
		strawberry: new FruitIcon({iconUrl: 'images/strawberry-icon.png'}),
		pomegranate: new FruitIcon({iconUrl: 'images/pomegranate-icon.png'})	
	};

	var tempMarker;
	var tempLabel;
	var tempAddress;
	var tempArray = <?php echo $mapped_markers; ?>;

	function Addmarker(markerArray) {
		for (var i = 0; i < markerArray.length; i++) {
//			console.log(markerArray[i]);				
			switch(markerArray[i]['type']) {
				case "orange":
					tempMarker = icons['orange'];
					break;
                                case "apple":
                                        tempMarker = icons['apple'];
                                        break;
                                case "lemon":
                                        tempMarker = icons['lemon'];
                                        break;
                                case "lime":
                                        tempMarker = icons['lime'];
                                        break;
                                case "blackberry":
                                        tempMarker = icons['blackberry'];
                                        break;
                                case "avocado":
                                        tempMarker = icons['avocado'];
                                        break;
                                case "plum":
                                        tempMarker = icons['plum'];
                                        break;
                                case "peach":
                                        tempMarker = icons['peach'];
                                        break;
                                case "fig":
                                        tempMarker = icons['fig'];
                                        break;
                                case "mandarin":
                                        tempMarker = icons['mandarin'];
                                        break;
                                case "apricot":
                                        tempMarker = icons['apricot'];
                                        break;
                                case "banana":
					tempMarker = icons['banana'];
					break;
                                case "blueberry":
                                        tempMarker = icons['blueberry'];
                                        break;
                                case "cherry":
                                        tempMarker = icons['cherry'];
                                        break;
                                case "grape":
                                        tempMarker = icons['grape'];
                                        break;
                                case "grapefruit":
                                        tempMarker = icons['grapefruit'];
                                        break;
                                case "guava":
                                        tempMarker = icons['guava'];
                                        break;
                                case "kiwi":
                                        tempMarker = icons['kiwi'];
                                        break;
                                case "mango":
                                        tempMarker = icons['mango'];
                                        break;
                                case "olive":
                                        tempMarker = icons['olive'];
                                        break;
                                case "pear":
                                        tempMarker = icons['pear'];
                                        break;
				case "raspberry":
                                        tempMarker = icons['raspberry'];
                                        break;
                                case "strawberry":
                                        tempMarker = icons['strawberry'];
                                        break;
                                case "pomegranate":
                                        tempMarker = icons['pomegranate'];
                                        break;
				default:
					tempMarker = icons['apple'];
			}
			if(markerArray[i]['address'] === "") {
				tempAddress = "";
			} else {
				tempAddress = "<br><b>Address:</b> " + markerArray[i]['address'];
			}
			tempLabel = "<b>Name:</b> " + markerArray[i]['name'] + "<br><b>Fruit:</b> " + markerArray[i]['type'] + "<br><b>Location:</b> " + markerArray[i]['lat'] + ", " + markerArray[i]['lng'] + tempAddress;
			L.marker([markerArray[i].lat, markerArray[i].lng],{icon: tempMarker}).addTo(map).bindPopup(tempLabel);		
//		this[dynamicname + i] = L.marker(
//           	[tempArray[i][0], tempArray[i][1]], {icon: redIcon}).addTo(map);
    		}
	}
	
	Addmarker(tempArray);


	var newMarker;
	var latLng;	
	var marker_lat;
	var marker_lng;
	var select_statement = '<select id="type">';

	// Load selct statement
	for (const [key, value] of Object.entries(icons)) {
		select_statement += "<option>" + key + "</option>";
	}	

	select_statement += '</select>';

	map.on('click', addMarker);

	function addMarker(e){
		var popupContent = "<table><tr><td>Name</td><td><input type='text' id='name'/></td></tr><tr><td>Location details</td><td><input type='text' id='address'/></td></tr><tr><td>Type</td><td>" + select_statement + "</td></tr><td><input type='button' value='Save & Close' onclick='saveData()'/></td><td align='right'><input type='button' value='Delete' onclick='deleteData()'/></td></tr></table>";

		newMarker = new L.marker(e.latlng).addTo(map).bindPopup(popupContent,{keepInView: true, closeButton: false}).openPopup();
		latLng = e.latlng;
		marker_lat = latLng.lat;
		marker_lng = latLng.lng;
//		console.log(newMarker);
		map.off('click',addMarker);		
//	        fetch('./insert.php?lat='+latLng['lat']+'&lng='+latLng['lng'])

	};

	function doNothing() {};

	function downloadUrl(url, callback) {
		var request = window.ActiveXObject ?
		new ActiveXObject('Microsoft.XMLHTTP') :
		new XMLHttpRequest;

	//        console.log(request);
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

		if (name !== "" && address !== "" && type !== "") {
//			console.log(name + " | " + address + " | " + type + " | " + marker_lat + " | " + marker_lng);
			map.closePopup();
			var url = "addrecord.php?name=" + name + "&address=" + address +
			"&type=" + type + "&lat=" + marker_lat + "&lng=" + marker_lng;
//			console.log(url);
	
			downloadUrl(url, function(data, responseCode) {
				if (responseCode == 200) {
//					console.log("Success!");
					window.location.reload();
				//          document.getElementById("message").innerHTML = "Location added.";
				}
				else {
//					console.log("failed to submit");
				}
		});
//			window.location.reload();
		}
		else {
			window.alert("Missing value");
//			console.log("missing value");
		}

    }

	function deleteData() {
//		console.log(newMarker);
		map.removeLayer(newMarker);
		map.closePopup();
		map.on('click', addMarker);
	}

    </script>
  
  </body>
</html>
