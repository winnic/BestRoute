<!DOCTYPE html >
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Get Distance</title>
    <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<?php
		include "dataLib.php";
		
//search all distinct buses
	$db=connect_to_bus();
    $stmt = $db->prepare("select pid,bus_num,bus_stationCHI,lat,longitube FROM bus where bus_num = 'CTB_95C-3'");
    $stmt->execute();
    $buses=$stmt->fetchAll(PDO::FETCH_ASSOC);
//for each buse station[i+1], find this distance
$buses=json_encode($buses);
//print_r($buses);
echo "<script type='text/javascript'>var buses=JSON.parse(JSON.stringify($buses));</script>";?>

<script type='text/javascript'>
var bus_num="";			  var data={};
var thisI=0;
function calculateDistance(){
for(;thisI<buses.length-1;thisI++){
	if(buses[thisI].bus_num==buses[thisI+1].bus_num){	
		var start = new google.maps.LatLng(
              parseFloat(buses[thisI].lat),
              parseFloat(buses[thisI].longitube));
		var end = new google.maps.LatLng(
              parseFloat(buses[thisI+1].lat),
              parseFloat(buses[thisI+1].longitube));
			 bus_num= buses[thisI].bus_num;
		calcDistance(start,end,buses[thisI+1].lat,buses[thisI+1].longitube);	  
			  }else{ thisI++;break;}
			  }
data[bus_num]=bus_num+"<br>";
window.setTimeout(data_get,2000);
}
function data_get(){
	var outputDiv = document.getElementById('get');
	outputDiv.innerHTML += data[bus_num]+"<br>";
	var arr = data[bus_num].split('<br>');
	var xhr = new XMLHttpRequest();
            xhr.open('POST', 'spanningTreeDB.php?action=update&Data='+arr);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            console.log( xhr+" "+arr);
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
            xhr.send();
			//window.setTimeout(calculateDistance,2000)
}



	function calcDistance(start,end,lat,lon){
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
			outputDiv.innerHTML = "<div id='outputDiv'><button  type='button' onclick='calculateDistance();'>Calculate distances</button></div>";
			//deleteOverlays();
		for (var i = 0; i < origins.length; i++) {
			var results = response.rows[i].elements;
			for (var j = 0; j < results.length; j++) {
				data[bus_num] +=lat+'meter in '
					+lon+'meter in '
					+results[j].distance.value + 'meter in '
					+ results[j].duration.value+',';
				outputDiv.innerHTML +=data[bus_num]+"<br>";
			}
		}
		}
	});
};

</script>
  </head>
  
  	<?php
		
	?>

  <body onload="calculateDistance();">
  <div id='testing'><div id='get'><button type='button' onclick='data_get();'>Process data</button><//div>
  	<div id='outputDiv'><button  type='button' onclick='calculateDistance();'>Calculate distances</button><//div></div>
    <div id="map" style="width: 800px; height: 450px"></div>

  </body>

</html>
