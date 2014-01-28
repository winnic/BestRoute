<?php
//echo "hello";
if(isset($_GET["action"])&&$_GET["action"]=="fetch_bus"){
	fetch_bus();
}
if(isset($_GET["action"])&&$_GET["action"]=="GMap_pos"){
	GMap_pos();
}
function connect_to_bus(){
	try {
		$db = new PDO('mysql:host=localhost;dbname=best_route;charset=utf8', 'root', '');
		} catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
    return $db;
}
function Select_Bus($input){
	$db = connect_to_bus();
	$stmt = $db->prepare("select distinct bus_num from bus order by bus_num");
	$stmt->execute();
	$sp_row = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach($sp_row as $row){
		foreach($row as $key=>$value){
			if($value)
				echo "<option class=$input>$value</option>";
		}
	}
}
function GMap_pos(){
	//echo "hello";
	$db = connect_to_bus();
	$stmt = $db->prepare("select bus_stationCHI,lat,longitube from bus where bus_num = ?");
	$stmt->execute(array($_GET["bus"]));
	$sp_row = $stmt->fetchAll(PDO::FETCH_ASSOC);
	header("Content-type: text/xml");
	echo '<markers>';
	foreach($sp_row as $row){
		echo '<marker ';
					echo 'address="' . parseToXML($row['bus_stationCHI']) . '" ';
		echo 'lat="' . $row['lat'] . '" ';
		echo 'lng="' . $row['longitube'] . '" ';
		echo 'type="input"';
		echo '/>';
	}
	echo '</markers>';
}
function parseToXML($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
$xmlStr=str_replace('>','&gt;',$xmlStr); 
$xmlStr=str_replace('"','&quot;',$xmlStr); 
$xmlStr=str_replace("'",'&#39;',$xmlStr); 
$xmlStr=str_replace("&",'&amp;',$xmlStr); 
return $xmlStr; 
} 
function fetch_bus(){
	$db = connect_to_bus();
	$stmt = $db->prepare("select bus_stationCHI,bus_stationENG,fee,lat,longitube from bus where bus_num = ?");
	$stmt->execute(array($_GET["bus"]));
	$sp_row = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo "<table border='1'><tr><th>車站名稱</th><th>Bus Station Name</th><th>車資 / fee</th></tr>";
	foreach($sp_row as $row){
		echo "<tr>";
		foreach($row as $key=>$value){
			if($key!="lat"&&$key!="longitube")
			echo "<td>".$value."</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}
?>