<?php 	
// Connecting, selecting database
function connect_to_bus(){
try {
$db = new PDO('mysql:host=localhost;dbname=best_route;charset=utf8', 'root', '');
    } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
    return $db;
}
// Create table
create_bus();
function create_bus(){
    $db=connect_to_bus();
    $stmt = $db->prepare("	CREATE TABLE IF NOT EXISTS bus (
							pid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
							bus_num VARCHAR(10) not NULL,
							station_id INT not NULL,
							bus_stationENG TEXT NOT NULL,
							bus_stationCHI TEXT NOT NULL,
							district TEXT NOT NULL,
							lat FLOAT,
							longitube FLOAT,
							accumulate_time FLOAT,
							fee FLOAT
						);");
    if ( ! $stmt->execute() ){print_r($stmt->errorInfo());exit;}
}
// Insert
function Insert($bus_num,$station_id,$bus_stationENG,$bus_stationCHI,$district,$lat,$longitube,$accumulate_time,$fee){
    $db=connect_to_bus();
    $q = $db->prepare("insert into bus (bus_num,station_id,bus_stationENG,bus_stationCHI,district,lat,longitube,accumulate_time,fee) values(?,?,?,?,?,?,?,?,?)");
    if (!$q->execute(array($bus_num,$station_id,$bus_stationENG,$bus_stationCHI,$district,$lat,$longitube,$accumulate_time,$fee))){print_r($q->errorInfo());exit;}
}

/* update and delete
$q = $db->prepare("UPDATE album SET size=?, time=?, path=? where name=?");
$q->execute(array($size, $time, $path, $filename));
$db=connect_to_bus();
$stmt = $db->prepare("drop table bus");
$stmt->execute();*/

// Selecting table, printing table
//////////////////for index.php///////////////////////////////
function SelectDistrict($point){
    $db=connect_to_bus();
    $stmt = $db->prepare("select DISTINCT district FROM bus");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        foreach($row as $key=>$value)
            if($value)
        echo "<option class=$point>$value</option>";
    }
}
if(isset($_GET["action"])){
	if($_GET["action"]=="Get_Latitiude_Longtitude"){
		Get_Latitiude_Longtitude($_GET[start],$_GET[dest]);
	}

	if($_GET["action"]=="SelectBusStation"){
		$db=connect_to_bus();
		$result=CharToUnicode($_GET[district]);
		$district=implode("",$result);
		$stmt = $db->prepare("select DISTINCT bus_stationCHI FROM bus where district=?");
		if (!$stmt->execute(array($district))){print_r($stmt->errorInfo());exit;}
		$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($rows as $row) {
			foreach($row as $key=>$value)
			echo "<option class=".$_GET[StartOrDestination].">$value</option>";
		}
	}
}

///////////////////////for generate data////////////////////////////////////////////////////////////
function parseToXML($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
$xmlStr=str_replace('>','&gt;',$xmlStr); 
$xmlStr=str_replace('"','&quot;',$xmlStr); 
$xmlStr=str_replace("'",'&#39;',$xmlStr); 
$xmlStr=str_replace("&",'&amp;',$xmlStr); 
return $xmlStr; 
} 

function Get_Latitiude_Longtitude($start, $end){
	$db = connect_to_bus();
	$start = CharToUnicode($start);
	$start = implode("",$start);
	$end = CharToUnicode($end);
	$end = implode("",$end);
	$stmt = $db->prepare("select DISTINCT bus_stationENG, bus_stationCHI, lat, longitube from bus where bus_stationCHI=?");
	$stmt->execute(array($start));
	$SP = $stmt->fetch();
	$stmt = $db->prepare("select DISTINCT bus_stationENG, bus_stationCHI, lat, longitube from bus where bus_stationCHI=?");
	$stmt->execute(array($end));
	$DP = $stmt->fetch();
	header("Content-type: text/xml");
	echo '<markers>';
// Iterate through the rows, printing XML nodes for each
//print_r($row);
  // ADD TO XML DOCUMENT NODE
	echo '<marker ';
	echo 'name="' . ($SP['bus_stationENG']) . '" ';
	echo 'address="' . parseToXML($SP['bus_stationCHI']) . '" ';
	echo 'lat="' . $SP['lat'] . '" ';
	echo 'lng="' . $SP['longitube'] . '" ';
	echo 'type="SP"';
	echo '/>';
	echo '<marker ';
	echo 'name="' . $DP['bus_stationENG'] . '" ';
	echo 'address="' . parseToXML($DP['bus_stationCHI']) . '" ';
	echo 'lat="' . $DP['lat'] . '" ';
	echo 'lng="' . $DP['longitube'] . '" ';
	echo '/>';
	echo '</markers>';
}
/////////////////////////////////The/////////////////////////////////////////////////////
/////////////////////////////////Searching/////////////////////////////////////////////////////
/////////////////////////////////Functions/////////////////////////////////////////////////////
function SearchAllBusIn($position){
    $db=connect_to_bus();
	$result=CharToUnicode($position);
    $position=implode("",$result);
    $stmt = $db->prepare("select bus_num FROM bus where bus_stationCHI=?");
    $stmt->execute(array($position));
    return  $stmt->fetchAll(PDO::FETCH_ASSOC);
}
if(isset($_GET["action"])&&$_GET["action"]=="eachBus_stations"){
	$db=connect_to_bus();
	$stmt=$db->prepare("select distinct bus_stationCHI FROM bus where lat=? and longitube=?");
	$stmt->execute(array($_GET["lnt"],$_GET["longitube"]));
	$stationA=$stmt->fetchAll(PDO::FETCH_ASSOC);
	$stmt = $db->prepare("select bus_num FROM bus where bus_stationCHI=?");
    $stmt->execute(array($stationA[0]["bus_stationCHI"]));
	$busesINstationA=$stmt->fetchAll(PDO::FETCH_ASSOC);
		$bus_stations=array();
		$bus_stations=(object)$bus_stations;
	for($i=0;$i<count($busesINstationA);$i++){
		$bus_stations->$busesINstationA[$i]["bus_num"]=SearchStations_withDtime($busesINstationA[$i]["bus_num"]);
	}
	$bus_stations->name=$stationA[0]["bus_stationCHI"];
	echo json_encode($bus_stations);
	return;
}

function SearchStations_withDtime($bus_num){
    $db=connect_to_bus();
    $stmt = $db->prepare("select bus_stationCHI,accumulate_time FROM bus where bus_num=?");
    $stmt->execute(array($bus_num));
    return  $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function SearchBus($bus_num){
    $db=connect_to_bus();
    $stmt = $db->prepare("select * FROM bus where bus_num=?");
    $stmt->execute(array($bus_num));
    return  $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function SearchStations($bus_num){
    $db=connect_to_bus();
    $stmt = $db->prepare("select bus_stationCHI FROM bus where bus_num=?");
    $stmt->execute(array($bus_num));
    return  $stmt->fetchAll(PDO::FETCH_ASSOC);
} 

if(isset($_GET["action"])&&$_GET["action"]=="find_LatLng"){
	$db=connect_to_bus();
	$stmt = $db->prepare("select lat, longitube FROM bus where bus_stationCHI=?");
	if (!$stmt->execute(array($_GET["bus_stationCHI"]))){print_r($stmt->errorInfo());exit;}
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
		echo json_encode($result);
		return;
}
if(isset($_GET["action"])&&$_GET["action"]=="theNearest"){
	$db=connect_to_bus();
	$stmt = $db->prepare("select bus_stationCHI,lat, longitube FROM bus");
	if (!$stmt->execute()){print_r($stmt->errorInfo());exit;}
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$x=$_GET["lat"];$y=$_GET["longitube"];
	for($i=0;$i<count($result);$i++){
		if(((float)$result[$i]["lat"]-(float)$x)>-0.0005&&((float)$result[$i]["lat"]-(float)$x)<0.0005)
		if(((float)$result[$i]["longitube"]-(float)$y)>-0.005&&((float)$result[$i]["longitube"]-(float)$y)<0.005){
			$stmt = $db->prepare("select bus_stationCHI FROM tree2 where bus_stationCHI=?");
			$stmt->execute(array($result[$i]["bus_stationCHI"]));
			$SP = $stmt->fetch(PDO::FETCH_ASSOC);
			echo json_encode($SP);
			return;
			$nearby=array();
			foreach($SP as $key=>$value){
				$Wcost["name"]=$value[bus_stationCHI];
				$Wcost["Wcost"]=explode("_",$value[W_COST] );
				foreach($Wcost["Wcost"] as $i=>$data)
				{
					$data=explode(",",$data);
					$data[1]=$lng=round($data[1], 3);
					$Wcost["Wcost"][$i]=$data;
					if(!$data[0]){
						unset($Wcost["Wcost"][$i]);		
					}
				}
			}
			array_push($nearby, $Wcost);
			
			
		}	
	}
}

/////////////////////////CharToUnicode/////////////////////////////////////////////////////////////
function CharToUnicode($chinese){ 
    $result=preg_split('/(?<!^)(?!$)/u', $chinese );//!!!!!!!!!!!!!!!!!!!!!!!!! 
    for($i=0; $i<count($result);$i++){
        $result[$i]="&#".base_convert(bin2hex(iconv("utf-8", "ucs-4", $result[$i])), 16, 10);  
    }  
	return $result;
}
////////////////////////for save walking time/////////////////////////////////////////////////////
if(isset($_GET["action"])&&$_GET["action"]=="saveWalkingTime"){
	$jsonData=json_decode($HTTP_RAW_POST_DATA);
	print_r( $jsonData);
    InsertIntoWtime($jsonData);
}

function InsertIntoWtime($jsonData){
    $db=connect_to_bus();
	foreach($jsonData as $key=>$value){
    $q = $db->prepare("update tree2 set W_COST=? where bus_stationCHI=?");
    if (!$q->execute(array($value,$key))){print_r($q->errorInfo());exit;}
	}
}

?>