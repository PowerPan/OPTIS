<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 17.10.13
 * Time: 14:09
 * To change this template use File | Settings | File Templates.
 */

set_time_limit(5000);
$start = microtime(true);
include_once("../classMySQL.php");
$mysql_service = new MySQL();
$mysql_firstlastdate = new MySQL();
$mysql_calendardates = new MySQL();
$mysql_update = new MySQL();
//Alle service herauslesen die in der trips stehen stehen
$mysql_service->query('select service_id,monday,tuesday,wednesday,thursday,friday,saturday,sunday,start_date,end_date from gtfs_calendar');
$min_percent = 51;
echo "<table border='1'>";
while($row_service=$mysql_service->fetchRow()){

    
    //Ersten und letzten Tag des Service herausfinden
    $firstday = $row_service['start_date'];
    $lastday = $row_service['end_date'];

    //Unixtimestamp für die Tage ermitteln weil damit einfacher zu rechnen ist
    $firstday_unix = strtotime($firstday);
    $lastday_unix = strtotime($lastday);

    //echo $firstday." ".$lastday;

    //Wochentag erster Tag ermitteln 0 für Sonntag bis 6 für Samstag
    $firstday_weekday = date('w',$firstday_unix);
    $tage = array();
    for($i = $firstday_unix; $i <= $lastday_unix;$i+=86400){
        $day = date("Y-m-d",$i);
        $wochentag = strtolower(date("l",$i));
        $tage[$day]['wochentag'] = $wochentag;
        $tage[$day]['tag'] = $day;
        if($row_service[$wochentag] == 1){
            $tage[$day]['faehrt'] = 1;
        }
        else
            $tage[$day]['faehrt'] = 0;
    }


    //Auslesen Aller Tage, an dennen der Service auch fährt
    $mysql_calendardates->query('select date,exception_type from gtfs_calendar_dates where service_id = '.$row_service['service_id']);
    while($row_calendardates = $mysql_calendardates->fetchRow()){
        if($row_calendardates['exception_type'] == 2)
            $row_calendardates['exception_type'] = 0;
        $tage[$row_calendardates['date']]['faehrt'] = $row_calendardates['exception_type'];
    }

    $counter_monday = 0;
    $counter_tuesday = 0;
    $counter_wednesday = 0;
    $counter_thursday = 0;
    $counter_friday = 0;
    $counter_saturday = 0;
    $counter_sunday = 0;

    $counter_monday_total = 0;
    $counter_tuesday_total = 0;
    $counter_wednesday_total = 0;
    $counter_thursday_total = 0;
    $counter_friday_total = 0;
    $counter_saturday_total = 0;
    $counter_sunday_total = 0;

    foreach($tage as $tag){
        if($tag['wochentag'] == 'monday'){
            $counter_monday_total++;
            if($tag['faehrt'] == 1)
                $counter_monday++;

        }
        if($tag['wochentag'] == 'tuesday'){
            $counter_tuesday_total++;
            if($tag['faehrt'] == 1)
                $counter_tuesday++;
        }
        if($tag['wochentag'] == 'wednesday'){
            $counter_wednesday_total++;
            if($tag['faehrt'] == 1)
                $counter_wednesday++;
        }
        if($tag['wochentag'] == 'thursday'){
            $counter_thursday_total++;
            if($tag['faehrt'] == 1)
                $counter_thursday++;
        }
        if($tag['wochentag'] == 'friday'){
            $counter_friday_total++;
            if($tag['faehrt'] == 1)
                $counter_friday++;
        }
        if($tag['wochentag'] == 'saturday'){
            $counter_saturday_total++;
            if($tag['faehrt'] == 1)
                $counter_saturday++;
        }
        if($tag['wochentag'] == 'sunday'){
            $counter_sunday_total++;
            if($tag['faehrt'] == 1)
                $counter_sunday++;
        }
    }

    @$percent_monday = (round((($counter_monday/$counter_monday_total)*10000))/100);
    @$percent_tuesday = (round((($counter_tuesday/$counter_tuesday_total)*10000))/100);
    @$percent_wednesday = (round((($counter_wednesday/$counter_wednesday_total)*10000))/100);
    @$percent_thursday = (round((($counter_thursday/$counter_thursday_total)*10000))/100);
    @$percent_friday = (round((($counter_friday/$counter_friday_total)*10000))/100);
    @$percent_saturday = (round((($counter_saturday/$counter_saturday_total)*10000))/100);
    @$percent_sunday = (round((($counter_sunday/$counter_sunday_total)*10000))/100);

    $faehrt_monday = 0;
    $faehrt_tuesday = 0;
    $faehrt_wednesday = 0;
    $faehrt_thursday = 0;
    $faehrt_friday = 0;
    $faehrt_saturday = 0;
    $faehrt_sunday = 0;

    if($percent_monday >= $min_percent)
        $faehrt_monday = 1;
    if($percent_tuesday >= $min_percent)
        $faehrt_tuesday = 1;
    if($percent_wednesday >= $min_percent)
        $faehrt_wednesday = 1;
    if($percent_thursday >= $min_percent)
        $faehrt_thursday = 1;
    if($percent_friday >= $min_percent)
        $faehrt_friday = 1;
    if($percent_saturday >= $min_percent)
        $faehrt_saturday = 1;
    if($percent_sunday >= $min_percent)
        $faehrt_sunday = 1;

    $faehrt_doch_nicht = Array();
    $faehrt_doch = Array();
    foreach($tage as $tag){
        if($faehrt_monday == 1){
            if($tag['faehrt'] == 0 and $tag['wochentag'] == "monday"){
                $faehrt_doch_nicht[] = $tag['tag'];
            }
        }
        if($faehrt_tuesday == 1){
            if($tag['faehrt'] == 0 and $tag['wochentag'] == "tuesday"){
                $faehrt_doch_nicht[] = $tag['tag'];
            }
        }
        if($faehrt_wednesday == 1){
            if($tag['faehrt'] == 0 and $tag['wochentag'] == "wednesday"){
                $faehrt_doch_nicht[] = $tag['tag'];
            }
        }
        if($faehrt_thursday == 1){
            if($tag['faehrt'] == 0 and $tag['wochentag'] == "thursday"){
                $faehrt_doch_nicht[] = $tag['tag'];
            }
        }
        if($faehrt_friday == 1){
            if($tag['faehrt'] == 0 and $tag['wochentag'] == "friday"){
                $faehrt_doch_nicht[] = $tag['tag'];
            }
        }
        if($faehrt_saturday == 1){
            if($tag['faehrt'] == 0 and $tag['wochentag'] == "saturday"){
                $faehrt_doch_nicht[] = $tag['tag'];
            }
        }
        if($faehrt_sunday == 1){
            if($tag['faehrt'] == 0 and $tag['wochentag'] == "sunday"){
                $faehrt_doch_nicht[] = $tag['tag'];
            }
        }


        if($faehrt_monday == 0){
            if($tag['faehrt'] == 1 and $tag['wochentag'] == "monday"){
                $faehrt_doch[] = $tag['tag'];
            }
        }
        if($faehrt_tuesday == 0){
            if($tag['faehrt'] == 1 and $tag['wochentag'] == "tuesday"){
                $faehrt_doch[] = $tag['tag'];
            }
        }
        if($faehrt_wednesday == 0){
            if($tag['faehrt'] == 1 and $tag['wochentag'] == "wednesday"){
                $faehrt_doch[] = $tag['tag'];
            }
        }
        if($faehrt_thursday == 0){
            if($tag['faehrt'] == 1 and $tag['wochentag'] == "thursday"){
                $faehrt_doch[] = $tag['tag'];
            }
        }
        if($faehrt_friday == 0){
            if($tag['faehrt'] == 1 and $tag['wochentag'] == "friday"){
                $faehrt_doch[] = $tag['tag'];
            }
        }
        if($faehrt_saturday == 0){
            if($tag['faehrt'] == 1 and $tag['wochentag'] == "saturday"){
                $faehrt_doch[] = $tag['tag'];
            }
        }
        if($faehrt_sunday == 0){
            if($tag['faehrt'] == 1 and $tag['wochentag'] == "sunday"){
                $faehrt_doch[] = $tag['tag'];
            }
        }
    }

    echo "<tr><th colspan='5'>".$row_service['service_id']."</th></tr>";
    echo "<tr><td>Montag</td><td>".$counter_monday_total."</td><td>".$counter_monday."</td><td>".$percent_monday."%</td><td>".$faehrt_monday."</td></tr>";
    echo "<tr><td>Dienstag</td><td>".$counter_tuesday_total."</td><td>".$counter_tuesday."</td><td>".$percent_tuesday."%</td><td>".$faehrt_tuesday."</td></tr>";
    echo "<tr><td>Mittwoch</td><td>".$counter_wednesday_total."</td><td>".$counter_wednesday."</td><td>".$percent_wednesday."%</td><td>".$faehrt_wednesday."</td></tr>";
    echo "<tr><td>Donnerstag</td><td>".$counter_thursday_total."</td><td>".$counter_thursday."</td><td>".$percent_thursday."%</td><td>".$faehrt_thursday."</td></tr>";
    echo "<tr><td>Freitag</td><td>".$counter_friday_total."</td><td>".$counter_friday."</td><td>".$percent_friday."%</td><td>".$faehrt_friday."</td></tr>";
    echo "<tr><td>Samstag</td><td>".$counter_saturday_total."</td><td>".$counter_saturday."</td><td>".$percent_saturday."%</td><td>".$faehrt_saturday."</td></tr>";
    echo "<tr><td>Sonntag</td><td>".$counter_sunday_total."</td><td>".$counter_sunday."</td><td>".$percent_sunday."%</td><td>".$faehrt_sunday."</td></tr>";
    echo "<tr><td>F&auml;hrt Doch</td><td colspan='4'>";
    print_r($faehrt_doch);
    echo "</td></tr>";
    echo "<tr><td>F&auml;hrt doch nicht</td><td colspan='4'>";
    print_r($faehrt_doch_nicht);
    echo "</td></tr>";

    $mysql_update->query("update gtfs_calendar set monday = ".$faehrt_monday.", tuesday = ".$faehrt_tuesday.", wednesday = ".$faehrt_wednesday.", friday = ".$faehrt_friday.", saturday = ".$faehrt_saturday.", sunday = ".$faehrt_sunday." where service_id = ".$row_service['service_id']."");
    $mysql_update->query("delete from gtfs_calendar_dates where service_id = ".$row_service['service_id']."");
    foreach ($faehrt_doch as $tag){
        $mysql_update->query("insert into gtfs_calendar_dates (service_id,date,exception_type) values (".$row_service['service_id'].",'".$tag."',1)");
    }
    foreach ($faehrt_doch_nicht as $tag){
        $mysql_update->query("insert into gtfs_calendar_dates (service_id,date,exception_type) values (".$row_service['service_id'].",'".$tag."',2)");
    }
}
echo "</table>";

