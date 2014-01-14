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
    $stmt = $db->prepare("select bus_stationCHI,lat,longitube FROM bus GROUP BY(bus_stationCHI)");
    $stmt->execute(array($bus_num));
    $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$json=json_encode($stations);
	echo "<script type='text/javascript'>var stations=JSON.parse(JSON.stringify($json));</script>"
	//find out all distinct sta

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
var data={};
var counterInData=0;
//var start=dynamicCenter;
var start=[];
var end=[];
var A={};
var index=localStorage.storedIndex;
localStorage.storedIndex=parseInt(index)+1;

//stations.forEach(function(info, index){
//for(var index=0;window.confirm(index);index++){
//if(window.confirm(index))
{
{console.log(index);
	dynamicCenter=new google.maps.LatLng(
              parseFloat(stations[index].lat),
              parseFloat(stations[index].longitube));
	window.k=0;
	window.googleLimit=25;
//initialize();
		start=[];	
		start.push(new google.maps.LatLng(
              parseFloat(stations[index].lat),
              parseFloat(stations[index].longitube)));
		A[1]=start;	

//////////////////////				
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
	calcDistance(dynamicCenter,end,A[1]);
	googleLimit=parseInt(googleLimit)+25;
	}
	,2000);
}counterInData=stations[index].bus_stationCHI;
data[counterInData]="";
}

//}
//);
	
	function calcDistance(dynamicCenter,end,start){
		var service = new google.maps.DistanceMatrixService();

		service.getDistanceMatrix(
	{
      origins: start,
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
	A[0]=end;
	calcDistance(dynamicCenter,end,A[1]);
	googleLimit=parseInt(googleLimit)+25;
	}
	,3000);
		}
		else{
			googleLimit-=25;
			k-=25;
		}
		} else {				console.log(k);console.log(index);
			if(k==617){
			//window.setTimeout(function (){setWtime();},2000*(5));
			}
			var origins = response.originAddresses;
			var destinations = response.destinationAddresses;
		for (var i = 0; i < origins.length; i++) {
			var results = response.rows[i].elements;			
			for (var j = 0; j < results.length; j++) {
			if(results[j].status=="OK")
			if(results[j].duration.value<600){
				data[stations[index].bus_stationCHI] +=end[j].jb+","+end[j].kb+","+results[j].distance.value + ','
					+ results[j].duration.value + '_';
				nearbyStations({"bus_stationCHI":destinations[j],"lat":end[j].jb,"longitube":end[j].kb
				});	
				}
			}
		}
		}

	});
};

function downloadUrl(data,url, callback) {
	var xhr = new XMLHttpRequest();
            xhr.open('POST', url);
			//xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if(xhr.readyState == 4)
                {
                    if(xhr.status != 200)
                        alert("Error code = " + new String(xhr.status));
                     else
                            {
                                document.getElementById('outputDiv').innerHTML=xhr.responseText;
                            }
                }
            };
            xhr.send(data);
}
function setWtime(){
	var jsonData=JSON.stringify(data);
	downloadUrl(jsonData,"dataLib.php?action=saveWalkingTime", function(){alert("done");});
};
	
</script>


	
	

  </head>
  <body> 

 <div id="map-canvas" style="width: 800px; height: 450px"></div>
	<div id="outputDiv"></div>
  </body>
</html>

