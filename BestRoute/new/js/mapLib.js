var dynamicCenter=new google.maps.LatLng(22.2506,114.15);

function initialize() {
  var circle = {};
	//stations.forEach(function (info,index){
  circle["SP"] = {
    center: new google.maps.LatLng(SP_DP[0].lat,SP_DP[0].longitube),
  };
  circle["DP"] = {
    center: new google.maps.LatLng(SP_DP[1].lat,SP_DP[1].longitube),
  };

  var cityCircle;
  window.mapOptions = {
    center: dynamicCenter,
    zoom: 12,
    mapTypeId: 'roadmap'
  };

 window.map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);

  for (var centre in circle) {
    var populationOptions = {
      strokeColor: '#FF0000',
      strokeOpacity: 0.5,
      strokeWeight: 1,
      fillColor: '#FF0000',
      fillOpacity: 0.35,
      map: map,
      center: circle[centre].center,
      radius: 100
    };
    cityCircle = new google.maps.Circle(populationOptions);
  }
  
  nearbyStationsV2(SP_DP[0],'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=O|FFFF00|000000',1);
  nearbyStationsV2(SP_DP[1],'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=D|FF0000|000000',2);
}
google.maps.event.addDomListener(window, 'load', initialize);

//////////////////generate nearby bus stations with info//////////////////////////////
var infoWindow = new google.maps.InfoWindow;
function nearbyStations(station){
  	var html = "<b>" + station.bus_stationCHI + "</b>";
  	//var icon = customIcons[type] || {};
  	var marker = new google.maps.Marker({
              map: window.map,
              position: new google.maps.LatLng(
                parseFloat(station.lat),
                parseFloat(station.longitube)),
              icon: "",
              shadow: ""
            });
  		  marker.setAnimation(google.maps.Animation.DROP);
  	bindInfoWindow(marker, map, infoWindow, html);
}

function nearbyStationsV2(station,thisIcon,pointer){
  	var html = "<b>" + station.bus_stationCHI + "</b>";
  	//var icon = customIcons[type] || {};
  	var marker = new google.maps.Marker({
              map: window.map,
              position: new google.maps.LatLng(
                parseFloat(station.lat),
                parseFloat(station.longitube)),
              icon: thisIcon,
              shadow: ""
            });
  		  		marker.setAnimation(google.maps.Animation.BOUNCE);
  				marker.setDraggable(true);
  	bindInfoWindowV2(marker, map, infoWindow, html,pointer);
}

		
function bindInfoWindowV2(marker, map, infoWindow, html,pointer) {
    google.maps.event.addListener(marker, 'click', function() {
      infoWindow.setContent(html);
      infoWindow.open(map, marker);
    });
	  google.maps.event.addListener(marker, 'dragend', function (){
			url="dataLib.php?action=theNearest&lat="+marker.getPosition().jb+"&longitube="+marker.getPosition().kb;
			AjaxCall("",url,function (response){ 
			var tmp=new Object();
			tmp=(JSON.parse(response));
			var keyword='keyword'+pointer;
			document.getElementById('chinese').innerHTML=tmp["bus_stationCHI"];
			document.getElementById(keyword).value=document.getElementById('chinese').innerHTML;
			document.getElementById('searchForm').style.display="block";
		});});
    }		
		
function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }
////////////////////////////////////////////////////////	
	
function AjaxCall(data, url, callback) {
    var xhr =window.ActiveXObject ?
        new ActiveXObject('Microsoft.XMLHTTP') :
        new XMLHttpRequest;
    xhr.open('GET', url);
	//xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
                if(xhr.readyState == 4)
                {
                    if(xhr.status != 200)
                        alert("Error code = " + new String(xhr.status));
                     else
                            {
								callback(xhr.responseText);
                            }
                }
            };
    xhr.send(data);
}


function calcDistance(start,end,time){
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
			//deleteOverlays();
			var result = response.rows[0].elements;
			result=result[0].duration.value;
			drivingTime.push(result);
			//debugger;
				//outputDiv.innerHTML +=output+"<br>";
		}
	});
	}

function calcRoute(start,end) {
  	window.directionsService = new google.maps.DirectionsService();
  	window.directionsDisplay=new google.maps.DirectionsRenderer();
  	directionsDisplay.setMap(map);
  	var request = {
        origin:start,
        destination:end,
  	  //waypoints:waypoint,
        travelMode: google.maps.DirectionsTravelMode.WALKING
  	};
    directionsService.route(request, function(response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
        directionsDisplay.setDirections(response);
      }
    });
    calcDistance(start,end);
}
	
function calcRoute(start,end,waypoint,Sname,Ename) {
  	window.directionsService = new google.maps.DirectionsService();
  	window.directionsDisplay=new google.maps.DirectionsRenderer();
  	directionsDisplay.setMap(map);

	if(waypoint){
		var request = {
			origin:start,
			destination:end,
			waypoints:waypoint,
			travelMode: google.maps.DirectionsTravelMode.DRIVING
		};
		}else{
		var request = {
			origin:start,
			destination:end,
			travelMode: google.maps.DirectionsTravelMode.DRIVING
		};
	}
    directionsService.route(request, function(response, status) {
  	console.log(response);
      if (status == google.maps.DirectionsStatus.OK) {
  		response.routes[0]["legs"][0].start_address+="<br>("+Sname+")";
        directionsDisplay.setDirections(response);
      }
    });
    calcDistance(start,end);
}
	
	