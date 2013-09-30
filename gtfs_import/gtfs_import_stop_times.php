<?php
/* Import Skript für die die Datei stop_times.txt */
include_once("../classMySQL.php");
$mysql = new MySQL();
if($_GET['delete'] == "true")
    $mysql->query("Truncate gtfs_stop_times");
$datei = fopen("data/stop_times.txt","r");
$firstrow = 1;
$rowcount = 0;

$insertcloums;
while(!feof($datei)){
    set_time_limit(5000);
    $row = fgets($datei);
    if($firstrow == 0){
        $row = explode(",",$row);
        $query = "insert into gtfs_stop_times (".$insertcloums.") values (";
        $insertcloumsarray = explode(",",$insertcloums);
        $columcount = 0;
        foreach($row as $data){

            /* Dieser Abschnitt kann wieder entfertn werden wenn in der stop_times keine Abfahrtszeiten > 23:56:59 mehr enthalten sind
            Aus 24:**:** wird 00:*:*
            */

            if($insertcloumsarray[$columcount] == "arrival_time"  || $insertcloumsarray[$columcount] == "departure_time"){
                $time = explode(":",$data);
                $time[0] = str_replace("24","00",$time[0]);
                $data = implode(":",$time);
                $querydata[] = "'".$data."'";
            }
            else
                $querydata[] = "'".str_replace('"','',utf8_decode($data))."'";
            $columcount++;
        }
        $querydata = implode(",",$querydata);
        $query .= $querydata.");";
        //echo $query;
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