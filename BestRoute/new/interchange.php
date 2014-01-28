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
//find StartingPoint=­»´ä¥J¡]¦¨³£¹D¡^ DestinationPoint=«n®Ô¤s¹D;
//43->118
//425->313?StartindDistrict=¼w»²¹D¦è&StartingPoint=¥¿µó&DestinationPoint=¤p¦èÆW?&DestinationDistrict=¤p¦èÆW¹D
//50->330   ?StartindDistrict=¤W¨ÈÍù²¦¹D&StartingPoint=­»´äÂ§»«©²&DestinationPoint=¬¥¶§µó&DestinationDistrict=¬¥¶§µó
	$_GET['StartingPoint']=implode("",CharToUnicode($_GET['StartingPoint']));
	$_GET['DestinationPoint']=implode("",CharToUnicode($_GET['DestinationPoint']));
    $stmt = $db->prepare("select bus_stationCHI,W_COST FROM tree2 where bus_stationCHI=?");
    $stmt->execute(array($_GET['StartingPoint']));
    $SP = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = $db->prepare("select bus_stationCHI,W_COST FROM tree2 where bus_stationCHI=?");
    $stmt->execute(array($_GET['DestinationPoint']));
	$DP = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$nearby=array();
	sepearte($SP);
	sepearte($DP);
	$SP_DP=array();
	$stmt = $db->prepare("select lat,longitube FROM bus where bus_stationCHI=?");
	$stmt->execute(array($SP[0]['bus_stationCHI']));
	$SP_DP[0]=$stmt->fetch(PDO::FETCH_ASSOC);
	$SP_DP[0]["bus_stationCHI"]=$SP[0]['bus_stationCHI'];
	$stmt->execute(array($DP[0]['bus_stationCHI']));
	$SP_DP[1]=$stmt->fetch(PDO::FETCH_ASSOC);
	$SP_DP[1]["bus_stationCHI"]=$DP[0]['bus_stationCHI'];
//sperate each station
	function sepearte($stations){
		//remove the extra _ in the end
		$length=strlen($stations[0]["W_COST"])-1;
		$stations[0]["W_COST"]=substr($stations[0]["W_COST"],0,$length);
		//
		global $nearby;
		foreach($stations as $key=>$value){
			$Wcost["name"]=$value['bus_stationCHI'];
			$Wcost["Wcost"]=explode("_",$value['W_COST'] );
			// $Wcost["Wcost"]=unset($Wcost["Wcost"][count(var)]);
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


<script src="js/mapLib.js"></script>
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
		/*	setTimeout(function (){	
				nearbyStations({"lat":thisNearby[0],"longitube":thisNearby[1],"bus_stationCHI":stations[index]["value"][i].name});
			}
			,3000);
		*/	

		});		
	});
	
// sps' buses'stations = dps' stations?
	var data="";
	var	JustWalk=0;

	setTimeout(function (){
		var dpNearby=stations[1]["value"];

		stations[0]["value"].forEach(function(thisInfo,thisIndex){
			//thisInfo = a nearby station
			if(!JustWalk){
			for(var key in thisInfo){
				//key =a bus pin(mainly) 
				if(thisInfo.name==stations[1].name){
					document.getElementById("output2").innerHTML="You are now at"+stations[0].name+"Just Walk To"+stations[1].name;
					window.JustWalk=1;
					window.start={"name":stations[0].name,"latlng":new google.maps.LatLng(parseFloat(SP_DP[0].lat),parseFloat(SP_DP[0].longitube))};
					window.end={"name":stations[1].name,"latlng":new google.maps.LatLng(parseFloat(SP_DP[1].lat),parseFloat(SP_DP[1].longitube))};
					calcRoute(start["latlng"],end["latlng"]);
					break;
				}
				if(key!="name"){
					for(var i=0;i<thisInfo[key].length;i++){		
					if(thisInfo[key][i].bus_stationCHI==thisInfo.name){
							var SPindex=i;
					}
					if(SPindex){
						for(var j=0;j<dpNearby.length;j++){
							for(var key2 in dpNearby[j]){
							if(key2=="name"){continue;}
								for(var k=0;k<dpNearby[j][key2].length;k++){
								if(dpNearby[j][key2][k].bus_stationCHI==thisInfo[key][i].bus_stationCHI){
									var DPindex=k;
								}
						if(DPindex){
							if(dpNearby[j].name==dpNearby[j][key2][k].bus_stationCHI){
								var tmp=key+key2;
								if(first==tmp||second==tmp){
									break;
								}
								var first=second;
								var second=tmp;
								{
								var Dtime=[];
								Dtime[0]=0;Dtime[1]=0;
								var counter=SPindex;
								for(;counter<i;counter++)
									Dtime[0]=parseInt(thisInfo[key][counter].accumulate_time)+parseInt(Dtime[0]);
								counter=DPindex;
								for(;counter<k;counter++)
									Dtime[1]=parseInt(dpNearby[j][key2][counter].accumulate_time)+parseInt(Dtime[1]);	

								walkingTime[0]=nearby[0]["Wcost"][thisIndex][3];
								walkingTime[1]=nearby[1]["Wcost"][j][3];	
								//find out the fast route without interchange
								if(thisInfo.name==thisInfo[key][i].bus_stationCHI){
									if(MinimumTravellingTime_oneTake<=(Dtime[0]+Dtime[1]+ parseInt(walkingTime[0])+parseInt(walkingTime[1]))){
										continue;
									}	
									MinimumTravellingTime_oneTake=(Dtime[0]+Dtime[1]+ parseInt(walkingTime[0])+parseInt(walkingTime[1]));
									window.data2="Go to "+thisInfo.name+" to "+"take "+key2+" to "+dpNearby[j].name+". <br>Travelling time is "+walkingTime[0]+"+"+walkingTime[1]+"+"+Dtime[0]+"+"+Dtime[1]+"="+Math.round(parseFloat(MinimumTravellingTime_oneTake)/60)+" mins<br>";
								}
								//
								if(MinimumTravellingTime<=(Dtime[0]+Dtime[1]+ parseInt(walkingTime[0])+parseInt(walkingTime[1]))){
									continue;
								}	
								MinimumTravellingTime=(Dtime[0]+Dtime[1]+ parseInt(walkingTime[0])+parseInt(walkingTime[1]));
								}
								window.interChange=thisInfo[key][i].bus_stationCHI;
								
								if(thisInfo.name==thisInfo[key][i].bus_stationCHI&&dpNearby[j].name==thisInfo[key][i].bus_stationCHI){
									var FirstTake="Maybe walk to desination is the fast way";
									var SecondTake="";
								}else if(thisInfo.name==thisInfo[key][i].bus_stationCHI){
									var FirstTake="Go to "+thisInfo.name+" to ";
									var SecondTake="take "+key2+" to "+dpNearby[j].name;
								}else if(dpNearby[j].name==thisInfo[key][i].bus_stationCHI){
									var FirstTake="Go to take "+key+" at "+thisInfo.name+" and take off at "+thisInfo[key][i].bus_stationCHI;
									var SecondTake="";
								}else{
									var FirstTake="Go to take "+key+" at "+thisInfo.name+" and take off at"+thisInfo[key][i].bus_stationCHI+", then ";
									var SecondTake="take "+key2+" to "+dpNearby[j].name;
								}
								
								data=FirstTake+SecondTake+". <br>Travelling time is "+walkingTime[0]+"+"+walkingTime[1]+"+"+Dtime[0]+"+"+Dtime[1]+"="+Math.round(parseFloat(MinimumTravellingTime)/60)+" mins<br>";
								
								//console.log(parseFloat(MinimumTravellingTime)/60);

								window.start={"name":thisInfo.name,"latlng":new google.maps.LatLng(parseFloat(nearby[0]["Wcost"][thisIndex][0]),nearby[0]["Wcost"][thisIndex][1])};
								window.end={"name":dpNearby[j].name,"latlng":new google.maps.LatLng(parseFloat(nearby[1]["Wcost"][j][0]),nearby[1]["Wcost"][j][1])};								
							break;//should break;
							}
							}
							}
							DPindex="";
							}
							//break;
							}
					}
					}
					//break;
				}
				SPindex="";
				//break;
			}
			}
		});
		}
	,4000);
	// time needed
	window.drivingTime=new Array();
	window.walkingTime=new Array();
	window.routes=new Array();
	window.MinimumTravellingTime=22222;
	window.MinimumTravellingTime_oneTake=22222;
	var bestChoice="";


	setTimeout(function (){
	if(!JustWalk){
		if((MinimumTravellingTime_oneTake-MinimumTravellingTime)/60>11){
			var url="dataLib.php?action=find_LatLng&bus_stationCHI="+escape(interChange);
			AjaxCall("", url, function (response){
					interChange=JSON.parse(response);
					interChange=[{location:new google.maps.LatLng(parseFloat(interChange["lat"]),parseFloat(interChange["longitube"])),stopover:true}];
					calcRoute(start["latlng"],end["latlng"],interChange,start["name"],end["name"]);
					document.getElementById("output").innerHTML="Origin="+SP_DP[0]["bus_stationCHI"]+" and Desination="+SP_DP[1]["bus_stationCHI"]+"<br><hr>"+data;
				});
		}else{
			calcRoute(start["latlng"],end["latlng"],false,start["name"],end["name"]);
			document.getElementById("output").innerHTML="Origin="+SP_DP[0]["bus_stationCHI"]+" and Desination="+SP_DP[1]["bus_stationCHI"]+"<br><hr>"+data2;
			console.log("when");
		}
	}
	},5000);
//show the best route
	function showNearby(){
		nearby.forEach(function (info,index){
			info["Wcost"].forEach(function (thisNearby,i){
			nearbyStations({"lat":thisNearby[0],"longitube":thisNearby[1],"bus_stationCHI":stations[index]["value"][i].name});
			});	
		});
	}
//	<button type='button' class='edit' onclick="function AAAAA(){document.getElementById('searchForm').style.display='block';};">Show search engine</button>	
</script>
    

</head>
  	<body> 
  		<div>
			<form id="searchForm" action="interchange.php" method="get" style="display:none"> 
				<input id="keyword1" name="StartingPoint" type="text" value=<?php echo $SP_DP[0]["bus_stationCHI"];?>>
				<input id="keyword2" name="DestinationPoint" type="text" value=<?php echo $SP_DP[1]["bus_stationCHI"];?>>
				<button type="submit">search</button>
			</form>
		</div>

	   	<div id="output2" style="color:red;width: 100%;magin-left=50%"></div>
		
		<button type='button' class='edit' onclick='showNearby();'>Show nearby stations</button>
	 	<div id="map-canvas" style="width: 100%; height: 400px" ></div>
	  	
	  	<div id="output" style="color:red;width: 100%"></div>
	  	
	  	<p id="chinese" style="display:none"></p>
  
  	</body>
</html>

