<?php
$success_path = $bus_path= $deep = array();
$success_path[0] = "7200";
if($_GET[action]=="update"){
	update_tree($_GET[Data]);
}
if($_GET[action]=="select"){
	selecting();
}
if($_GET[action]=="mincost"){
	//echo "type = ".gettype($_GET[DP]);
	if(($_GET[DP]!="[object Object]")&&($_GET[SP]!="[object Object]")){
		search();
	}
}
if($_GET[action]=="reduce"){
	reduce_choas();
}
function reduce_choas(){
	for($k=1;$k<=617;$k++){
	$pid = $k;
	$two_D_arr =$arr= array();
	$db = connect_to_bus();
	$stmt = $db->prepare("select COST from tree2 where pid = ?");
	$stmt->execute(array($pid));
	$sp_row = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$branch = explode("||",$sp_row[0]["COST"]);
	print_r($branch);
	foreach($branch as $sp_data){
		$sp_data=explode("=>",$sp_data);
		array_push($two_D_arr,$sp_data);
	}
	$end = count($two_D_arr);
	for($j=0;$j<$end;$j++){
		if($two_D_arr[$j]){
		$tmp_result=$two_D_arr[$j][1]."=>".$two_D_arr[$j][2]."=>";
		$check_dup = $two_D_arr[$j][2];
		for($i=0;$i<$end;$i++){
			if($check_dup==$two_D_arr[$i][2]){
				$tmp_result .= "//".$two_D_arr[$i][0];
				unset($two_D_arr[$i]);
			}
		}
		array_push($arr,$tmp_result);
		}
	}
	$final=implode("||",$arr);
	echo "<br>".$final;
	$stmt1 = $db->prepare("update tree2 set COST=? where pid=?");
	$stmt1 ->execute(array($final,$pid));
	}
}
function SP(){
	$SP = CharToUnicode($_GET[SP]);
	return implode("",$SP);
}
function DP(){
	$DP = CharToUnicode($_GET[DP]);
	return implode("",$DP);
}
function search(){//
	global $success_path;
	global $bus_path;
	 branch_search(0,"",SP());
	 get_route();
}
//http://www.shop135.ierg4210.org/CSCI4140/spanningTreeDB.php?action=mincost&SP=%E8%A5%BF%E5%8D%80%E8%AD%A6%E7%BD%B2&DP=%E7%84%A1%E9%99%90%E6%A5%B5%E5%BB%A3%E5%A0%B4
function get_route(){
	global $success_path;
	global $bus_path;
	//print_r($bus_path);
	$route=array();
	$result = "";
	$count = 0;
	$different_path=array();
	$tmp = $bus_path[count($success_path)-2];
	$best_route = explode("!!",$tmp);
	unset ($best_route[0]);
	$counter=array();
	$oldCounter=array();
	
		for($i=1;$i<count($best_route)+1;$i++){
			$best_route[$i]=explode("//",$best_route[$i]);
		}
print_r($best_route);
	$index=1;
	for($i=1;$i<count($best_route)+1;$i++){
		//$best_route[$i]=explode("//",$best_route[$i]);
		//break;
		$oldCounter=$counter;
		for($j=1;$j<count($best_route[$index]);$j++){
		for($k=1;$k<count($best_route[$i]);$k++)
		{
			if(strcmp($best_route[$i][$k],$best_route[$index][$j])==0)
				$counter[$j]++;
		}		
		}
						//print_r($counter);
				//print_r(count($best_route[$index]));
		for($l=1;$l<count($best_route[$index]);$l++){
		if(count($counter)==1){
		echo "take ".$best_route[$index][$l]." to ".$best_route[$i-1][0]."<br>";
		$index=$i;
		$counter=array();
		$i--;
		continue;}
			if($oldCounter[$l]==$counter[$l])
				unset ($counter[$l]);
				}
	}
	echo "<br>take ".$best_route[$index][1]." to ".$best_route[$i-1][0]."<br>";

			//print_r($counter);
	foreach($best_route as $bus_stop){
		$single_stop = explode("//",$bus_stop);
		array_push($route,$single_stop);
	}

	/*
	for($i=0;$i<(count($route)-1);$i++){
		$j=$i+1;
			for($p=0;$p<(count($route[$i])-1);$p++){
				for($k=0;$k<(count($route[$i+1])-1);$k++){ //+1 有bug
					if($route[$i][$p]=="CTB_7-1"){
						//connected
						$count++;
						$result .= $route[$i][$j]."=>".$route[$i][0]."to".$route[$i+1][0];
						//echo $result;
					}
			}
		}
		//$result.=$result;
		//echo $result;
	}*/
}
function branch_operation($current_cost,$cost_to_next,$bus_route,$bus_to_next,$next_node){ //check if the destination point has been matched or not.
	global $success_path;
	global $bus_path;
	if(!$success_path){
		if($next_node==DP()){		//handling reached case
			$current_cost += $cost_to_next;
			$bus_route .= "!!".$next_node.$bus_to_next;
			array_push($bus_path,$bus_route);
			array_push($success_path,$current_cost);
			//if match,reset the cost to the preivous node
			//return $success_path;
		}
		else{								//Continue to look for deeper branch
			$current_cost += $cost_to_next; //唔可以係咁加,點reset番去之前個node?
			$bus_route .= "!!".$next_node.$bus_to_next;
			branch_search($current_cost,$bus_route,$next_node);
			//return $success_path;
		}
	}
	else{
		if(($next_node==DP())){		//handling reached case
			$current_cost += $cost_to_next;
			$bus_route .= "!!".$next_node.$bus_to_next;
			if($current_cost<$success_path[count($success_path)-1]){
				array_push($success_path,$current_cost);
				array_push($bus_path,$bus_route);
			}
			//return $success_path;
		}
		else{								//Continue to look for deeper branch
			$current_cost += $cost_to_next; //唔可以係咁加,點reset番去之前個node?
			$bus_route .= "!!".$next_node.$bus_to_next;
			if($current_cost>$success_path[count($success_path)-1]){ //handling cost already exceeding the fastest (MAY HAVE BUG, THE ARRAY PATH, smaller index = higher cost)
					//return $success_path;
				}
			else{
					branch_search($current_cost,$bus_route,$next_node);
				}
		}
	}
}
function branch_search($current_cost,$bus_route,$station){ //looping over the node for it's branches based on the incoming node
	global $success_path;
	global $bus_path;
	global $deep;
	$count=0;
	$db = connect_to_bus();
	$stmt = $db->prepare("select * from tree2 where bus_stationCHI =?");
	$stmt->execute(array($station));
	$sp_row = $stmt->fetchAll(PDO::FETCH_ASSOC); //contain in the [0] container;
	for($p=0;$p<count($deep);$p++){
		if(($deep[$p])==($sp_row[0]["pid"])){
			$count=1;
		}
	}
	if($count==0){
		array_push($deep,$sp_row[0]["pid"]);
		$branch = explode("||",$sp_row[0]["COST"]);
		foreach($branch as $sp_data){
			$sp_data=explode("=>",$sp_data);
			branch_operation($current_cost,$sp_data[0],$bus_route,$sp_data[2],$sp_data[1]);
		}
	}
	else{
	}
}
function connect_to_bus(){
	try {
		$db = new PDO('mysql:host=localhost;dbname=data;charset=utf8', 'root', 'pwd');
		} 
	catch (PDOException $e) {
		print "Error!: " . $e->getMessage() . "<br/>";
		die();
	}
    return $db;
}
function selecting(){
	$db = connect_to_bus();
	$stmt = $db->prepare("select bus_stationCHI from tree2");
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	echo "<select>";
	foreach ($rows as $row){
		foreach($row as $key=>$value){
			if($value)
			echo "<option class=$point>$value</option>";
		}
	}
	echo "</select>";
}
function select_bus(){
	create_tree();
	$db = connect_to_bus();
	$stmt = $db->prepare("select DISTINCT bus_stationCHI from bus");
	$stmt->execute();
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	foreach ($rows as $row){
		foreach($row as $key=>$value){
			$arr=$arr1=$arr2=array();
			$stmt1 = $db->prepare("select DISTINCT bus_num,accumulate_time,next_station from bus where bus_stationCHI = ? ");
			$stmt1->execute(array($value));
			$test = $stmt1->fetchAll(PDO::FETCH_ASSOC);
			foreach($test as $line){
				if($line[accumulate_time]){
					$result = $line[bus_num]."=>".$line[accumulate_time]."=>".$line[next_station];
					array_push($arr,$result);
				}
			}
			$tmp=implode("||",$arr);
			print_r($tmp);
			echo "<br>";
			insert_into_tree($value,$tmp);
		}
	}
}
function create_tree(){
	$db = connect_to_bus();
	$stmt = $db->prepare(" 	CREATE TABLE IF NOT EXISTS tree2(
							pid INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
							bus_stationCHI TEXT NOT NULL,
							COST TEXT NOT NULL,
							W_COST TEXT NOT NULL
							);");
	if ( ! $stmt->execute() ){print_r($stmt->errorInfo());exit;}
}
function insert_into_tree($bus_station,$cost){
	$db = connect_to_bus();
	$q = $db->prepare("insert into tree2(bus_stationCHI,COST) values(?,?)"); 
    if (!$q->execute(array($bus_station,$cost))){print_r($q->errorInfo());exit;}
}
function update_tree($data){
$j=0;
$tmp=$tmp2=$loc_arr=array();
	$data = explode("<br>",$data);
	$data=$data[0];
	$data=explode(",",$data);
	$bus_num=$data[0];
	//echo count($data);
	for($i=1;$i<=count($data);$i++){
		$line = explode("meter in",$data[$i]);
		$result = array($line[2]=>$line[3]);
		array_push($tmp,$result);
	}
	$db = connect_to_bus();
	$q = $db->prepare("select pid from bus where bus_num=?");
	$q->execute(array($bus_num));
	$test = $q->fetchAll(PDO::FETCH_ASSOC);
	foreach($test as $l){
		foreach($l as $key1=>$pid){
			$temp = $tmp[$j];
			print_r($temp);
			$loc = $loc_arr[$j];
			foreach($temp as $key=>$value){
				if($pid!=count($test)){
					$next_pid=$pid+1;
					$q=$db->prepare("select bus_stationCHI from bus where pid=?");
					$q->execute(array($next_pid));
					$location = $q->fetch();
					$loc = $location[bus_stationCHI];
				}
				else{
					$loc = "";
				}
				$q=$db->prepare("update bus set accumulate_time=?,distance_to_next=?,next_station=? where pid=?");
				$q->execute(array($value,$key,$loc,$pid));
			}
			$j++;
		}
	}
}
function CharToUnicode($chinese){
    $result=preg_split('/(?<!^)(?!$)/u', $chinese );//!!!!!!!!!!!!!!!!!!!!!!!!! 
    for($i=0; $i<count($result);$i++){
		$num=base_convert(bin2hex(iconv("utf-8", "ucs-4", $result[$i])), 16, 10);  
		if($num<100){
			$num="000".$num;
		}
        $result[$i]="&#".$num;
    }  
	return $result;
}
?>