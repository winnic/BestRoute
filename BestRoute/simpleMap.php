<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple Map</title>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
		<?php
// database
	include "dataLib.php";
	$db=connect_to_bus();
//find StartingPoint=香港仔（成都道） DestinationPoint=南朗山道;
	$_GET[StartingPoint]=implode("",CharToUnicode($_GET[StartingPoint]));
	$_GET[DestinationPoint]=implode("",CharToUnicode($_GET[DestinationPoint]));
    $stmt = $db->prepare("select bus_stationCHI,W_COST FROM tree2 where bus_stationCHI=?");
    $stmt->execute(array($_GET[StartingPoint]));
    $SP = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = $db->prepare("select bus_stationCHI,W_COST FROM tree2 where bus_stationCHI=?");
    $stmt->execute(array($_GET[DestinationPoint]));
	$DP = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
		$nearby=array();
	sepearte($SP);
	sepearte($DP);
	$SP_DP=array();
	$stmt = $db->prepare("select lat,longitube FROM bus where bus_stationCHI=?");
	$stmt->execute(array($SP[0][bus_stationCHI]));
	$SP_DP[0]=$stmt->fetch(PDO::FETCH_ASSOC);
	$stmt->execute(array($DP[0][bus_stationCHI]));
	$SP_DP[1]=$stmt->fetch(PDO::FETCH_ASSOC);
	
//sperate each station
function sepearte($stations){
	global $nearby;
foreach($stations as $key=>$value){
	$Wcost["name"]=$value[bus_stationCHI];
	$Wcost["Wcost"]=explode("_",$value[W_COST] );
	foreach($Wcost["Wcost"] as $i=>$data)
	{
		$data=explode(",",$data);
		$data[1]=roundUp($data[1]);
		$Wcost["Wcost"][$i]=$data;
		if(!$data[0]){
			unset($Wcost["Wcost"][$i]);		
		}
	}
}
	array_push($nearby, $Wcost);
}
//round up longtitute and set markers
function roundUp($lng){
	$lng+=0.000001;
	$lng=round($lng, 3);;
	return (string)$lng;
}
//SP nearby stations and DP as well
	$nearby=json_encode($nearby);
	echo "<script type='text/javascript'>var nearby=JSON.parse(JSON.stringify($nearby));</script>";
//find a route with only by one bus
/*	$stmt3 = $db->prepare("select distinct bus_stationCHI FROM bus where lat=? and longitube=?");
    $stmt3->execute(array("22.2497", "114.156"));
    $Astations = $stmt3->fetchAll(PDO::FETCH_ASSOC);
		print_r($Astations);	echo "<br>";	*/
//transit data to js
	$json=json_encode($SP_DP);
	echo "<script type='text/javascript'>var SP_DP=JSON.parse(JSON.stringify($json));</script>";	
?>

	<script src="mapLib.js"></script>
<script type="text/javascript">
var stations=[];
	nearby.forEach(function (info,index){
		info["Wcost"].forEach(function (thisNearby,i){
//search the quickest route with least interchange
//find bus and its stations in these bus stations
		stations[index]=new Object();
		stations[index]["value"]=[];
		stations[index]["name"]=info["name"];

		var url="dataLib.php?action=eachBus_stations&lnt="+thisNearby[0]+"&longitube="+thisNearby[1];
		AjaxCall({"lat":thisNearby[0],"longitube":thisNearby[1]}, url, function (response){
			stations[index]["value"][i]=JSON.parse(response);
		});
			setTimeout(function (){	
				nearbyStations({"lat":thisNearby[0],"longitube":thisNearby[1],"bus_stationCHI":stations[index]["value"][i].name});
			}
			,10000);
			

		});		
	});
	
// sps' buses'stations = dps' stations?
setTimeout(function (){
	var dpNearby=stations[1]["value"];
	stations[0]["value"].forEach(function(thisInfo,thisIndex){
		for(var key in thisInfo){
			if(key!="name"){
				for(var i=0;i<thisInfo[key].length;i++){
					for(var j=0;j<dpNearby.length;j++){
						if(thisInfo[key][i].bus_stationCHI==dpNearby[j].name){
						walkingTime[0].push(window.nearby[0]["Wcost"][thisIndex][3]);
						walkingTime[1].push(window.nearby[1]["Wcost"][j][3]);
						var start=new google.maps.LatLng(parseFloat(nearby[0]["Wcost"][thisIndex][0]),nearby[0]["Wcost"][thisIndex][1]);
						var end=new google.maps.LatLng(parseFloat(nearby[1]["Wcost"][j][0]),nearby[1]["Wcost"][j][1]);
						calcDistance(start,end);
						document.getElementById('output').innerHTML+="plx walk to "+thisInfo.name+" and take "+key+" to "+thisInfo[key][i].bus_stationCHI+"second<br>";
						routes.push({"Sname":thisInfo.name,"Ename":thisInfo[key][i].bus_stationCHI,"start":start,"end":end, "bus":key});
						break;
						}
						}
				}
			}
			break;
		}
	});
	}
,12000);
// time needed
window.drivingTime=new Array();
window.walkingTime=new Array();
window.walkingTime[0]=new Array();
window.walkingTime[1]=new Array();
window.routes=new Array();
window.MinimumTravellingTime=22222;
var bestChoice="";
setTimeout(function (){
	for(var i=0;i<drivingTime.length;i++){
	var total= parseInt(drivingTime[i])+parseInt(walkingTime[0][i])+parseInt(walkingTime[1][i]);
		if(total<MinimumTravellingTime){
		MinimumTravellingTime= total;
		bestChoice=i;
		}
		document.getElementById('output').innerHTML+="Driving Time = "+drivingTime[i]+" and Walking time = "+walkingTime[0][i]+"and"+walkingTime[1][i]+"<br>";
	}
	document.getElementById('output2').innerHTML+="The Best Route : Go to "+routes[bestChoice].Sname+" to take "+routes[bestChoice].bus+", and take off at "+routes[bestChoice].Ename+". Travelling time is "+Math.round(parseFloat(MinimumTravellingTime)/60)+"mins";
	calcRoute(routes[bestChoice].start,routes[bestChoice].end);
},14000);

//show the best route

	
</script>
    

  </head>
  <body> 
    <div id="output2" style="color:red;width: 100%;magin-left=50%"></div>
 <div id="map-canvas" style="width: 1000px; height: 600px"></div>
  <div id="output" style="color:red;width: 50%"></div>
    <div id="output2" style="color:red;width: 100%;magin-left=50%"></div>

  </body>
</html>

