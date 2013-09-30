<?php
/* Import Skript für die die Datei routes.txt */
include_once("../classMySQL.php");
$mysql = new MySQL();
if($_GET['delete'] == "true")
    $mysql->query("Truncate gtfs_routes");
$datei = fopen("data/routes.txt","r");
$firstrow = 1;
$rowcount = 0;
$insertcloums;
while(!feof($datei)){
    set_time_limit(5000);
    $row = fgets($datei);
    if($firstrow == 0){
        $row = explode(",",$row);
        $query = "insert into gtfs_routes (".$insertcloums.") values (";
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