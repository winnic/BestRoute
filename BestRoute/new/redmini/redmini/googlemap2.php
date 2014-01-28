<!DOCTYPE html >
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>PHP/MySQL & Google Maps Example</title>
	<style>
		#showMap{
			width: 150px;
			height: 50px;
			border: 0;
			font-size: 18px;
			background-color: #1abc9c;
			color: white;
			position: absolute;
			top: 0%;
			right: 0%;
		}
		h2{
			color: #222;
			font-weight: 400;
			text-decoration: none;
			left: 75px;
			position: relative;
			font-size: 1.6rem;
			width: 130px;
		}
		#goBack{
			width: 60px;
			height: 52px;
			background-color: #1abc9c;
			position: absolute;
			top: 0%;
			left: 0%;
		}
		#backImg{
			display:block;
			margin:auto;
		}
		#menuHr{
			position: absolute;
			left:0%;
			right:0%;
			top: 42px;
		}
	</style>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.2.min.js"> </script>
    <script type="text/javascript">
    //<![CDATA[
	var waypoint =[];
	var firstRed=$("table").first().find("tr[style='color:red']").first().find("th").first().text();
	var lastRed=$("table").first().find("tr[style='color:red']").last().find("th").first().text();
	
	 var start = new google.maps.LatLng(
              parseFloat(busInfo[firstRed].lat),
              parseFloat(busInfo[firstRed].longitube));
  var end = new google.maps.LatLng(
              parseFloat(busInfo[lastRed].lat),
              parseFloat(busInfo[lastRed].longitube));
	
	var directionsService = new google.maps.DirectionsService();
	var directionsDisplay;
    var customIcons = {
      restaurant: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_blue.png',
        shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
      },
      bar: {
        icon: 'http://labs.google.com/ridefinder/images/mm_20_red.png',
        shadow: 'http://labs.google.com/ridefinder/images/mm_20_shadow.png'
      }
    };

	var map ;var infoWindow ;
	
    function load() {
      map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(22.2506,114.15),
        zoom: 12,
        mapTypeId: 'roadmap'
      });

      infoWindow = new google.maps.InfoWindow;
		directionsDisplay=new google.maps.DirectionsRenderer({suppressMarkers: true,
		suppressInfoWindows: true,});
	  directionsDisplay.setMap(map);
	  
      // Change this depending on the name of your PHP file
	  //var dataUrl = "dataLib.php?action=Get_Latitiude_Longtitude&start="+sp+"&dest="+dp;
	//////////////////
		var numOfPoints=parseInt((lastRed-firstRed)/9+1);
	//////////////////
        for (var i = firstRed; i <=lastRed; i++) {
			var name = busInfo[i].bus_stationENG;
			var address = busInfo[i].bus_stationCHI;
			var type = "";
			var point = new google.maps.LatLng(
				parseFloat(busInfo[i].lat),
				parseFloat(busInfo[i].longitube));
			var html = "<b>" + name + "</b> <br/>" + address;
			var icon = customIcons[type] || {};
			var marker = new google.maps.Marker({
				map: map,
				position: point,
				icon: icon.icon,
				shadow: icon.shadow
			});
			///////creat waypoints///////////
			if(i%numOfPoints==0&i!=0)
			{
			  waypoint.push({
			  location:point,
			  stopover:true});
			}		
			//calcRoute(waypoint);
			//////////////////
			  bindInfoWindow(marker, map, infoWindow, html);
        }
		console.log(waypoint);
		calcRoute(waypoint);
		/////////////////////////
		var citymap = {};
		citymap['chicago'] = {
			center:start,
		};
		citymap['chicago'] = {
			center:end,
		};
		for (var city in citymap) {
			// Construct the circle for each value in citymap. We scale population by 20.
			var populationOptions = {
			  strokeColor: '#FF0000',
			  strokeOpacity: 0.8,
			  strokeWeight: 2,
			  fillColor: '#FF0000',
			  fillOpacity: 0.35,
			  map: map,
			  center: citymap[city].center,
			  radius: 200
			};
			cityCircle = new google.maps.Circle(populationOptions);
		}
	
	}

    function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }

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

	
	 
	function calcRoute(waypoint) {
  var request = {
      origin:start,
      destination:end,
	  waypoints:waypoint,
      travelMode: google.maps.DirectionsTravelMode.DRIVING
  };
  directionsService.route(request, function(response, status) {
    if (status == google.maps.DirectionsStatus.OK) {
		console.log(response);
		directionsDisplay.setDirections(response);
    }
  });
}

	function calcDistance(){
		var service = new google.maps.DistanceMatrixService();
		service.getDistanceMatrix(
	{
      origins: [start],
      destinations: [end],
      travelMode: google.maps.TravelMode.DRIVING,
      unitSystem: google.maps.UnitSystem.METRIC,
      avoidHighways: false,
      avoidTolls: false
    }, function (response, status) 
	{
		if (status != google.maps.DistanceMatrixStatus.OK) {
		alert('Error was: ' + status);
		} else {
			var origins = response.originAddresses;
			var destinations = response.destinationAddresses;
			var outputDiv = document.getElementById('outputDiv');
			outputDiv.innerHTML = '';
			//deleteOverlays();

		for (var i = 0; i < origins.length; i++) {
			var results = response.rows[i].elements;
			//addMarker(origins[i], false);
			for (var j = 0; j < results.length; j++) {
				//addMarker(destinations[j], true);
				outputDiv.innerHTML += origins[i] + ' to ' + destinations[j]
					+ ': ' + results[j].distance.value + ' in '
					+ results[j].duration.value + '<br>';
			}
		}
		}
	});
}

    //]]>
		
	var showMapAtFistTime=1;
	$("button#showMap").click(function(){	  
		if($("button#showMap").text()=="Map"){
			if(showMapAtFistTime==1){
				$("#mapWrapper").show();
				load();
				showMapAtFistTime=0;
				}else{
				$("#mapWrapper").show();
			}
			$("button#showMap").text("Back");
			}else{
			$("#mapWrapper").hide();
			$("button#showMap").text("Map");
		}
		//google.maps.event.trigger(map, 'resize');
		//map.setCenter(start);
	});

	
  </script>

  </head>

  <body onload="" >
      <div id="mapWrapper" style="width: 100%; height: 100%; position: fixed; top:50px; left:0px; display:none;">
    <div id="map" style="width: 100%; height: 100%; position: fixed; "></div>
	</div>
  </body>

</html>
