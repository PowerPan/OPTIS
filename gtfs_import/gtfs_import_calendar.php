<?php
/* Import Skript für die die Datei calendar.txt */
include_once("../classMySQL.php");
include_once("function.php");
$mysql = new MySQL();
if($_GET['delete'] == "true")
    $mysql->query("Truncate gtfs_calendar");
if($datei = fopen("data/calendar.txt","r")){
$firstrow = 1;
$rowcount = 0;
$insertcloums;
while(!feof($datei)){
    set_time_limit(5000);
    $row = fgets($datei);
    if($firstrow == 0){
        $row = explode(",",$row);
        $query = "insert into gtfs_calendar (".$insertcloums.") values (";
        $insertcloumsarray = explode(",",$insertcloums);
        $columcount = 0;
        foreach($row as $data){
            if($insertcloumsarray[$columcount] == "start_date" or $insertcloumsarray[$columcount] == "end_date\n"){
                $querydata[] = "'".gtfsdate_2_mysqldate($data)."'";
            }

            else
                $querydata[] = "'".str_replace('"','',utf8_decode($data))."'";
            $columcount++;
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
}
echo "keine Calendar.txt vorhanden";
?>