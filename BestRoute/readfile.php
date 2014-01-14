<?php
function connect_to_bus(){
try {
$db = new PDO('mysql:host=localhost;dbname=data;charset=utf8', 'root', 'pwd');
    } catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
    return $db;
}
// Create table
create_bus();
readcsv();
function create_bus(){
    $db=connect_to_bus();
    $stmt = $db->prepare("	CREATE TABLE IF NOT EXISTS testing (
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
    $q = $db->prepare("insert into testing (bus_num,station_id,bus_stationENG,bus_stationCHI,district,lat,longitube,accumulate_time,fee) values(?,?,?,?,?,?,?,?,?)");
    if (!$q->execute(array($bus_num,$station_id,$bus_stationENG,$bus_stationCHI,$district,$lat,$longitube,$accumulate_time,$fee))){print_r($q->errorInfo());exit;}
}

function readcsv(){
	$row = 1;
	$brand = "CTB";
	$arr=array("","A","B","C","D","E","F","M","P","S","K","X","R","U","N");
	$arr2=array("","N","M");
	for($i=1;$i<1000;$i++){
		for($j=0;$j<=14;$j++){
			for($y=0;$y<=2;$y++){
				for($k=1;$k<=5;$k++){
					$station_id=1;
					$bus_num1 = $arr2[$y].$brand."_".$i.$arr[$j]."-".$k;
					$bus_num = $arr2[$y].$brand."/".$i.$arr[$j]."-".$k.".csv";
					echo "<br>".$bus_num."</br>";
					if (($handle = fopen($bus_num, "r")) !== FALSE) {
						while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
							$num = count($data);
							echo "<p> $num fields in line $row: <br /></p>\n";
							$row++;
							for ($c=3; $c < $num; $c++) {
								echo $data[$c] . "<br/>\n";
							}
							Insert($bus_num1,$station_id,$data[6],$data[9],"",$data[3],$data[4],$data[5]);
							$station_id++;
						}
						fclose($handle);
					}
				}
			}
		}
	}
}
?>