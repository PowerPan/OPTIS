<?php
/* Import Skript für die die Datei stops.txt */
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../classMySQL.php");
$mysql = new MySQL();
if($_GET['delete'] == "true")
    $mysql->query("Truncate gtfs_shapes");
$datei = fopen("data/shapes.txt","r");
$firstrow = 1;
$rowcount = 0;
$insertcloums;
while(!feof($datei)){
	set_time_limit(5000);
	$row = fgets($datei);
	if($firstrow == 0){
		$row = explode(",",$row);
        $query = "insert into gtfs_shapes (".$insertcloums.") values (";
        foreach($row as $data){
            $querydata[] = "'".str_replace('"','',utf8_decode($data))."'";
        }
        $querydata = implode(",",$querydata);
        $query .= $querydata.");";
        if($querydata != ""){
            $mysql->query($query);
            $querydata = "";
            $rowcount++;
        }
	}
    else{
        $insertcloums = $row;
    }
	$firstrow = 0;
}
fclose($datei);

echo $rowcount." Zeilen in Datenbank geschrieben";
?>