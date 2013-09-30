<?php
/* Import Skript für die die Datei calendar_dates.txt */
include_once("../classMySQL.php");
include_once("function.php");
$mysql = new MySQL();
if($_GET['delete'] == "true")
    $mysql->query("truncate gtfs_calendar_dates");
$datei = fopen("data/calendar_dates.txt","r");
$firstrow = 1;
$rowcount = 0;
$insertcloums;
while(!feof($datei)){
    set_time_limit(5000);
    $row = fgets($datei);
    if($firstrow == 0){
        $row = explode(",",$row);
        $query = "insert into gtfs_calendar_dates (".$insertcloums.") values (";
        $insertcloumsarray = explode(",",$insertcloums);
        $columcount = 0;
        foreach($row as $data){
            if($insertcloumsarray[$columcount] == ("date"))
                $querydata[] = "'".gtfsdate_2_mysqldate($data)."'";
            else
                $querydata[] = "'".str_replace('"','',utf8_decode($data))."'";
            $columcount++;
        }
        $querydata = implode(",",$querydata);
        $query .= $querydata.");";
        if($querydata != ""){
            $mysql->query($query);
            $rowcount++;
        }
        $querydata = "";
    }
    else{
        $insertcloums = $row;
    }
    $firstrow = 0;
}
fclose($datei);

echo $rowcount." Zeilen in Datenbank geschrieben";
?>