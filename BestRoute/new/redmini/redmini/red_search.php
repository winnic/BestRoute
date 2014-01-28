<?php
if($_GET["StartingPoint"]&&$_GET["DestinationPoint"]){
	$thisSp=$_GET["StartingPoint"];
	$thisDp=$_GET["DestinationPoint"];	
}else{
	header("Location: http://localhost/BestRoute/new/index.php"); //!! change to dynamic to index.php
	exit;
}

include "red_dataLib.php";

    $SP_buses=SearchAllBusIn($thisSp);
    $DP_buses=SearchAllBusIn($thisDp);
		echo "<script type='text/javascript'>var sp=\"$thisSp\";var dp=\"$thisDp\";console.log(sp);console.log(dp);</script>";
	//TODO
	$thisSp=implode("",CharToUnicode($thisSp));
	$thisDp=implode("",CharToUnicode($thisDp));
	
		$accessOnce=1;
		$Interchange=1;

for($i=0;$i<count($SP_buses);$i++)
    for($j=0;$j<count($DP_buses);$j++){
        if($SP_buses[$i]["bus_num"]==$DP_buses[$j]["bus_num"]){
			if($accessOnce==1){
				echo "<a href='redIndex.php'><div id='goBack'><img id='backImg' src='img/back.png' height='38px'/></div></a><h2>".$SP_buses[$i]["bus_num"]."</h2> 		<hr id='menuHr' />   <button id='showMap'>Map</button>";
				echo "<div id='outputDiv'></div>";
			}else{
				echo "<h2>".$SP_buses[$i]["bus_num"]."</h2>";
			}
            $busInfo=SearchBus($SP_buses[$i]["bus_num"]);
			
			/*if($k==1){
			//createXML($busInfo);
			$k=0;
			}*/
			if($accessOnce==1){
			$json=json_encode($busInfo);
			}
			echo "<script type='text/javascript'>var busInfo=JSON.parse(JSON.stringify($json));console.log(busInfo.length)</script>";
            echo "<table id=".$SP_buses[$i]["bus_num"]." border='1' cellspacing=5pt cellpadding=10pt style='border:1pt solid #7777ff;'>";
            echo "<tbody><tr><th>Statoin ID</th><th>bus_stationCHI</th>";
			
			$color="";
            for($k=0;$k<count($busInfo);$k++){		
			if(strcmp($thisSp,$busInfo[$k]["bus_stationCHI"])==0){$color="color:red";}

/*			if(((int)$busInfo[$k][station_id]-(int)$IDs[0]>=0)&&(int)$busInfo[$k][station_id]<=8){
				$color="color:red";
			}else{
				$color="";
			}*/
                echo "<tr style=$color><th>".$busInfo[$k]["station_id"]."</th><th>".$busInfo[$k]["bus_stationCHI"]."</th></tr>";
			if(strcmp($thisDp,$busInfo[$k]["bus_stationCHI"])==0){$color="";}
            }
            echo "</tbody></table>";
			$id=$SP_buses[$i]["bus_num"];
			if(strcmp($color,"color:red")==0){
				echo "<script type='text/javascript'>console.log(\"$id\");document.getElementById(\"$id\").style.display='none';</script>";
				$color="";
				//break;
			}
			if($accessOnce==1){
				include "googlemap2.php";	
			$accessOnce=0;
			}
			
			$Interchange=0;
			break;
            //  [pid] => 1 ["bus_num"] => 1-1.csv [station_id] => 1 [bus_stationENG] => Happy Valley (Upper) [bus_stationCHI] => 跑馬地（上）總站 [district] => [lat] => 22.2645 [longitube] => 114.189 [accumulate_time] => 0.5 ["fee"] => 3.4 
    }
}
//not found any direct route
//find all stations of each bus of both SP and DP;
//for each station in each bus compare to all stations in each bus in DP
//if match, find the bus info of this SPbus and this DPbus
//and interchange=this station
if($Interchange){
	for($j=0;$j<count($DP_buses);$j++)
		$interStations_DP[$j]=SearchStations($DP_buses[$j]["bus_num"]);
		//print_r($interStations_DP);echo "<br><br>";
		//print_r($SP_buses);echo "<br><br>";
	for($i=0;$i<count($SP_buses);$i++){
		$interStations_SP=SearchStations($SP_buses[$i]["bus_num"]);
		for($numBusStation=0;$numBusStation<count($interStations_SP);$numBusStation++)
			for($j=0;$j<count($interStations_DP);$j++)
				for($k=0;$k<count($interStations_DP[$j]);$k++)
					if($interStations_SP[$numBusStation]["bus_stationCHI"]==$interStations_DP[$j][$k]["bus_stationCHI"])
					{
						echo "this bus is".$SP_buses[$i]["bus_num"]."<br>";
						echo "this bus is".$DP_buses[$j]["bus_num"]."<br>";
						break;
					}
			//print_r($interStations_SP);
	}

}

?>