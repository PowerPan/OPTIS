<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 08.08.13
 * Time: 10:09
 * To change this template use File | Settings | File Templates.
 */
//set_time_limit(5000);
include_relative("../classMySQL.php");
include_relative("function.php");
function include_relative($file)
{
    $bt = debug_backtrace();
    $old = getcwd();
    chdir(dirname($bt[0]['file']));
    include($file);
    chdir($old);
}
set_time_limit(5000);

$today = date('Y-m-d');
$tomorrow = date('Y-m-d',time()+86400);
$day3 = date('Y-m-d',time()+86400*2);
$day4 = date('Y-m-d',time()+86400*3);
$day5 = date('Y-m-d',time()+86400*4);
$day6 = date('Y-m-d',time()+86400*5);
$day7 = date('Y-m-d',time()+86400*6);
$day8 = date('Y-m-d',time()+86400*7);
$day9 = date('Y-m-d',time()+86400*8);


echo $today;
echo "<br><br>";
echo $tomorrow;
echo "<br><br>";

echo $today;
echo "<br><br>";
fill_departures($today,1);
find_exception_service($today);
echo $tomorrow;
echo "<br><br>";
fill_departures($tomorrow,0);
find_exception_service($tomorrow);
echo $day3;
echo "<br><br>";
fill_departures($day3,0);
find_exception_service($day3);
echo $day4;
echo "<br><br>";
fill_departures($day4,0);
find_exception_service($day4);
echo $day5;
echo "<br><br>";
fill_departures($day5,0);
find_exception_service($day5);
echo $day6;
echo "<br><br>";
fill_departures($day6,0);
find_exception_service($day6);
echo $day7;
echo "<br><br>";
fill_departures($day7,0);
find_exception_service($day7);
echo $day8;
echo "<br><br>";
fill_departures($day8,0);
find_exception_service($day8);
echo $day9;
echo "<br><br>";
fill_departures($day9,0);
find_exception_service($day9);



echo "<br>****<br>";
/*
find_exception_service($tomorrow);
find_exception_service($day3);
find_exception_service($day4);
find_exception_service($day5);
find_exception_service($day6);
find_exception_service($day7);
find_exception_service($day8);*/

delete_last_stop();



function fill_departures($date,$truncate){
    $mysql = new MySQL();
    //Departues füllen
    $mysql->query("CALL read_departures('".$date."',".$truncate.")");
}

function find_exception_service($date){
    $mysql = new MySQL();
    //Abfahrten die Laut ausnahem heute doch stattfinden hinzufügen
    $mysql->query("select service_id from departures where date between '".$date." 00:00:00' and '".$date." 23:59:59'");
    while($row = $mysql->fetchRow()){
        $departureserviceid[] = $row['service_id'];
    }
    //print_r($departureserviceid);
    //echo "----";
    $mysql->query("select service_id from gtfs_calendar_dates where date between '".$date." 00:00:00' and '".$date." 23:59:59' and exception_type = 1");
    if($mysql->count() > 0){
        while($row = $mysql->fetchRow()){
            $calendardatesserviceid[] = $row['service_id'];
        }
        //print_r($calendardatesserviceid);
        foreach($calendardatesserviceid as $serviceid){
            if(!in_array($serviceid,$departureserviceid)){
                //Diese Müssen hinzugefuegt werden
                $mysql->query("CALL add_service_to_departures('".$serviceid."','".$date."')");
                //echo $serviceid;
                //echo "<br>";
            }
        }
    }
}
function delete_last_stop(){
    $mysql = new MySQL();
    $mysql2 = new MySQL();
    //Die Letzten Haltestellen eines jeden rips finden und löschen
    $mysql->query("select
                        st.trip_id
                        ,st.stop_id
                        ,st.stop_sequence
                    from
                        gtfs_stop_times st
                    where
                        st.stop_sequence = (select MAX(st2.stop_sequence) from gtfs_stop_times st2 where st2.trip_id = st.trip_id) ");

    while($row = $mysql->fetchRow()){
        $mysql2->query("delete from departures where stop_id = ".$row['stop_id']." and trip_id = ".$row['trip_id'].";"   );
    }
    //$deletestring = implode(" ",$deletestring);
}

