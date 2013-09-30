<?php
include_once("../classMySQL.php");
$mysql = new MySQL();
if($_GET['delete'] == "true")
    $datei = fopen("data/transfers.txt","r");
$firstrow = 1;
$rowcount = 0;
while(!feof($datei)){
	set_time_limit(5000);
	$row = fgets($datei);
	if($firstrow == 0){
		$row = explode(",",$row);
		$mysql->query("insert into gtfs_transfers (from_stop_id,to_stop_id,transfer_type,min_transfer_time,from_trip_id,to_trip_id) values ('".$row[0]."','".$row[1]."','".$row[2]."','".$row[3]."','".$row[4]."','".$row[5]."')");
		//print_r($row);	
		//echo "\n";
		$rowcount++;
	}
	$firstrow = 0;		
}
fclose($datei);

echo $rowcount." Zeilen in Datenbank geschrieben";
?>