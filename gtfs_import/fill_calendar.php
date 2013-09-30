<?php
/**
 * Created by JetBrains PhpStorm.
 * User: johannes
 * Date: 26.04.13
 * Time: 22:37
 * To change this template use File | Settings | File Templates.
 */
set_time_limit(5000);
$start = microtime(true);
include_once("classMySQL.php");
$mysql_service = new MySQL();
$mysql_firstlastdate = new MySQL();
$mysql_calendardates = new MySQL();

$mysql_update = new MySQL();

//Prozentzahl die erreicht werden muss, dass gesagt werden kann, das ein Service Regelmäßig stattfindet
$percent = 0.80;

//Alle service herauslesen die in der trips stehen stehen
$mysql_service->query('select service_id from gtfs_calendar ');
while($row_service=$mysql_service->fetchRow()){

    //Auslesen Aller Tage, an dennen der Service auch fährt
    $mysql_calendardates->query('select date from gtfs_calendar_dates where service_id = '.$row_service['service_id']);
    while($row_calendardates = $mysql_calendardates->fetchRow())
        $servicedates[] = $row_calendardates['date'];

    //Ersten und letzten Tag des Service herausfinden
    $mysql_firstlastdate->query('select min(date)min,max(date)max from gtfs_calendar_dates where service_id = '.$row_service['service_id']);
    $row_firstlastday = $mysql_firstlastdate->fetchRow();
    $firstday = $row_firstlastday['min'];
    $lastday = $row_firstlastday['max'];

    //Unixtimestamp für die Tage ermitteln weil damit einfacher zu rechnen ist
    $firstday_unix = strtotime($firstday);
    $lastday_unix = strtotime($lastday);

    //Wochentag erster Tag ermitteln 0 für Sonntag bis 6 für Samstag
    $firstday_weekday = date('w',$firstday_unix);

    //Array mit allen Tagen erstellen
    $alldays = Array();
    $weekday = -1;
    while($weekday != $firstday_weekday){

        //Wenn $weekday = -1 dann erster durchlauf
        if($weekday == -1){
            $weekday = $firstday_weekday;
            $day_unix = $firstday_unix;
            $day_unix_count = $day_unix;
        }
        else
            $day_unix = $day_unix_count;
        do{
            //Datum des Tages holen
            $day = date('Y-m-d',$day_unix);

            //Datum in das Array alldays schreiben
            $alldays[$weekday][] = $day;

            //tag um eins erhöhen
            $day_unix = $day_unix + 604800; //604800s = 1 Woche

            //Feststellen ob Serive an diesem Tag stattfindet.
            if(@in_array($day,$servicedates)){
                $service_drive[$weekday][] = $day;
            }
            else{
                $service_not_drive[$weekday][] = $day;
            }

        }while($day_unix < $lastday_unix);
        $weekday++;
        $day_unix_count = $day_unix_count + 86400;
        if($weekday > 6)
            $weekday = 0;
    };
    //AB hier wird geschaut ob ein Trip reglmäßig stattfindet;
    //Schleife die Jedem Wochentag durchläuft
    $result .="service_id: ".$row_service['service_id']."\n";
    $count_weekservices =0;
    for($i = 0;$i <= 6;$i++){
        $result .= count($service_drive[$i])." - ".count($alldays[$i])." - ";
        $result .= (count($service_drive[$i])/count($alldays[$i]));
        $result .= "\n";

        if($i == 0)
            $weekday_day = "sunday";
        if($i == 1)
            $weekday_day = "monday";
        if($i == 2)
            $weekday_day = "tuesday";
        if($i == 3)
            $weekday_day = "wednsday";
        if($i == 4)
            $weekday_day = "thursday";
        if($i == 5)
            $weekday_day = "friday";
        if($i == 6)
            $weekday_day = "saturday";
        $count_weekservices = $count_weekservices + count($service_drive[$i]);

        //Wenn an mindestens __% der Tage gefahren wird dann in st dei Fahrt reglmäßig
        //und wird in der calendard datei auch so vermekr, an allen Tagen die der Service
        //nicht fährt wird in der calendar_dates gesetzt, dass dieser nicht fährt
        if((count($service_drive[$i])/count($alldays[$i])) >= $percent ){

            //$mysql_update->query('update gtfs_calendar set')

        }
    }
    $service_drive = NULL;
    $service_not_drive = NULL;
    $alldays = NULL;
    $servicedates = NULL;
    $result .= "--------------------------------\n\n";
}
$result .= microtime(true)-$start.' Sekunden gebraucht';
echo str_replace("\n","<br>",$result);
$datei = fopen("result_service_frequency.txt",w);
fwrite($datei,$result);
fclose($datei);