<html>
<header>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8">
</header>
<body>
<?php 	
include "dataLib.php";
    $db=connect_to_bus();
	//$q = $db->prepare("show tables");
    //$q->execute();
	//echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
  //  $q = $db->prepare("select bus_stationCHI, lat,longitube from bus where bus_num='CTB_70P-4'");
 //   if(!$q->execute(array("CTB_511-3","CTB_6-1"))){print_r($q->errorInfo());exit;}
//	print_r(($q->fetchAll(PDO::FETCH_ASSOC)));
	$q = $db->prepare("select * from redmini ");
	if(!$q->execute()){print_r($q->errorInfo());exit;}
	print_r(($q->fetchAll(PDO::FETCH_ASSOC)));
	
//http://localhost/BestRoute/new/redmini/red_search.php?StartindDistrict=Mong+Kok&StartingPoint=%E6%B4%97%E8%A1%A3%E8%A1%97%EF%BC%8C%E9%87%91%E9%9B%9E%E5%BB%A3%E5%A0%B4&DestinationPoint=%E8%8F%AF%E5%AF%8C%EE%91%B3%E8%8F%AF%E6%A8%82%E6%A8%93&DestinationDistrict=Pok+Fu+Lam
?>
</body>
</html>

