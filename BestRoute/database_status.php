<?php 	
include "dataLib.php";
    $db=connect_to_bus();
	//$q = $db->prepare("show tables");
    //$q->execute();
	//echo json_encode($q->fetchAll(PDO::FETCH_ASSOC));
  //  $q = $db->prepare("select bus_stationCHI, lat,longitube from bus where bus_num='CTB_70P-4'");
 //   if(!$q->execute(array("CTB_511-3","CTB_6-1"))){print_r($q->errorInfo());exit;}
//	print_r(($q->fetchAll(PDO::FETCH_ASSOC)));
	$q = $db->prepare("select * from tree2 ");
	if(!$q->execute()){print_r($q->errorInfo());exit;}
	print_r(($q->fetchAll(PDO::FETCH_ASSOC)));
	

?>