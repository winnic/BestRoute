<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Circles</title>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
		<?php
// database
include "dataLib.php";
   $db=connect_to_bus();
    $stmt = $db->prepare("select DISTINCT bus_stationCHI,lat,longitube FROM bus");
    $stmt->execute(array($bus_num));
    $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$json=json_encode($stations);
	echo "<script type='text/javascript'>var stations=JSON.parse(JSON.stringify($json));</script>"

?>
    <script>
// Create an object containing LatLng, population.
var dynamicCenter=new google.maps.LatLng(22.2506,114.15);


function initialize() {
	var citymap = {};
citymap['chicago'] = {
  center: dynamicCenter,
};
var cityCircle;
  var mapOptions = {
    center: dynamicCenter,
        zoom: 12,
    mapTypeId: 'roadmap'
  };

  window.map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

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
      radius: 400
    };
    cityCircle = new google.maps.Circle(populationOptions);
  }
}
google.maps.event.addDomListener(window, 'load', initialize);

////////////////////////////////////////////////////////
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
	bindInfoWindow(marker, map, infoWindow, html);
}

function bindInfoWindow(marker, map, infoWindow, html) {
      google.maps.event.addListener(marker, 'click', function() {
        infoWindow.setContent(html);
        infoWindow.open(map, marker);
      });
    }

    </script>
	

<script type='text/javascript'>
//walking time<60*10s => mark it in google map
//use cal distance function
var data={"0":""};
var counterInData=0;
var start=dynamicCenter;
var end=[];
var A={};

var index=-1;

//stations.forEach(function(info, index){
//for(var index=0;window.confirm(index);index++){
function goOnLooping()
{
//if(window.confirm(index))
{
{console.log(index);
	dynamicCenter=new google.maps.LatLng(
              parseFloat(stations[index+1].lat),
              parseFloat(stations[index+1].longitube));
	window.k=0;
	window.googleLimit=25;
//initialize();
var loopToGetInfo=setInterval(function (){
	var end=[];
	if(googleLimit<stations.length)
	for(;k<googleLimit;k++){	
		end.push(new google.maps.LatLng(
              parseFloat(stations[k].lat),
              parseFloat(stations[k].longitube)));
	}
	else{
		for(;k<stations.length;k++){	
			end.push(new google.maps.LatLng(
              parseFloat(stations[k].lat),
              parseFloat(stations[k].longitube)));
		}	
		clearInterval(loopToGetInfo);
	}
	A[0]=end;
	calcDistance(dynamicCenter,end);
	googleLimit=parseInt(googleLimit)+25;
	}
	,2000);
}counterInData=stations[index+1].bus_stationCHI;
data[counterInData]="";
}

index++;
}//}
//);

	
	function calcDistance(dynamicCenter,end){
		var service = new google.maps.DistanceMatrixService();
		service.getDistanceMatrix(
	{
      origins: [dynamicCenter],
      destinations: end,
      travelMode: google.maps.TravelMode.WALKING,
      unitSystem: google.maps.UnitSystem.METRIC,
      avoidHighways: false,
      avoidTolls: false
    }, function (response, status) 
	{
		if (status != google.maps.DistanceMatrixStatus.OK) {
		setTimeout(function (){},2500);
		if(googleLimit>stations.length){	
			googleLimit-=25;
			k-=(stations.length-googleLimit);
			googleLimit+=25;
			var loopToGetInfo=setInterval(function (){
			var end=[];
			if(googleLimit<stations.length)
				for(;k<googleLimit;k++){	
					end.push(new google.maps.LatLng(
						parseFloat(stations[k].lat),
						parseFloat(stations[k].longitube)));
				}
				else{
					for(;k<stations.length;k++){	
						end.push(new google.maps.LatLng(
							parseFloat(stations[k].lat),
							parseFloat(stations[k].longitube)));
					}	
					clearInterval(loopToGetInfo);
				}
			console.log(k);
			A[0]=end;
			calcDistance(start,end);
			googleLimit=parseInt(googleLimit)+25;
			}
			,1000);
		}
		else{
			googleLimit-=25;
			k-=25;
		}
		console.log(k);
		} else {
			var origins = response.originAddresses;
			var destinations = response.destinationAddresses;
		for (var i = 0; i < origins.length; i++) {
			var results = response.rows[i].elements;				
			for (var j = 0; j < results.length; j++) {
			if(results[j].status=="OK")
			if(results[j].duration.value<600){
				data[counterInData] +=end[j].jb+","+end[j].kb+";"+results[j].distance.value + 'meter in '
					+ results[j].duration.value + 'second<br>';
				nearbyStations({"lat":end[j].jb,"longitube":end[j].kb
				});
				}
			}
		}
		}

	});
};

	
</script>


	
	

  </head>
  <body> 
    <div id="map-canvas" style="width: 800px; height: 450px"></div>
	<button  id="goOnLooping" type='button' onclick='goOnLooping();'>Calculate distances</button>
  </body>
</html>

