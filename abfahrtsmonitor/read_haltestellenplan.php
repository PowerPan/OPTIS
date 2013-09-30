<?php
header('Content-Type: text/html; charset=utf8');
date_default_timezone_set("Europe/Berlin");
include_once("../classMySQL.php");
$mysql = new MySQL();
$stop_id = $_GET['stop_id'];
$rows = $_GET['rows'];
$limitstart = $_GET['limitstart'];
if(!isset($_GET['limitstart']))
    $limitstart = 0;


//Ortsnamen der stadtion
$query = "select stop_name from gtfs_stops where stop_id = ".$stop_id;
$mysql->query($query);
$row=$mysql->fetchRow();
$stadt = explode(" ",$row['stop_name']);
$stadt = $stadt[0];



//Check auf Parentstation
$query = "select stop_id from gtfs_stops where parent_station = ".$stop_id."";
//echo $query;
$mysql->query($query);
//echo $mysql->count();
if($mysql->count() > 0){
    $stop_id = Array();
    while($row = $mysql->fetchRow())
        $stop_id[] = $row['stop_id'];
}
if(is_array($stop_id))
    $stop_id = implode(',',$stop_id);

//Alle Stops finden die zu der gleichen partent_sation gehören um die auszufiltern die an einer der Haltestellen auch Enden

$query_stops = "select s.stop_id from gtfs_stops s where s.stop_id = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$_GET['stop_id']." ) or s.parent_station = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$_GET['stop_id']." and s2.parent_station != 0) or s.parent_station = ".$_GET['stop_id']."";

//echo $query_stops;
$mysql->query($query_stops);
while($row = $mysql->fetchRow())
    $stops_id[] = $row['stop_id'];

$stops_id = implode(',',$stops_id);

if($rows == "")
    $rows = 5;
$query="SELECT
	UNIX_TIMESTAMP(d.date) departure_time,
	d.destination,
    d.trip_id,
	d.route,
	d.infotext
from
	departures d
where
    d.stop_id in (".$stop_id.")
    and d.date > NOW()
    and ( (select gst2.stop_id from gtfs_stop_times gst2 where gst2.trip_id = d.trip_id order by gst2.stop_sequence desc limit 1) not in (".$stop_id.") or trip_id IS NULL)
group by departure_time,destination,route
order by date,route,destination
limit ".$limitstart.",".$rows."
					";
//echo $query;
$mysql->query($query);

$tagheute = date('N');
$wocheheute = date("W");
$morgenproblem=0;
while($row = $mysql->fetchRow()){
    $jsonrowdata['trip_id'] = $row['trip_id'];
    $jsonrowdata['route_short_name'] = $row['route'];
    $jsonrowdata['trip_headsign'] = utf8_encode(str_replace($stadt." ","",$row['destination']));
    $jsonrowdata['infotext'] = utf8_encode($row['infotext'])." ";
    $jsonrowdata['departure_time'] = $row['departure_time'];
    $abfahrt = ceil(($jsonrowdata['departure_time'] - time())/60);
    if($abfahrt < 2)
        $jsonrowdata['departure_time'] = "sofort";
    else if($abfahrt < 60)
        $jsonrowdata['departure_time'] = $abfahrt."&nbsp;min";
    else if(($tagabfahrt = date('N',$jsonrowdata['departure_time'])) != $tagheute or ($wocheabfahrt = date('W',$jsonrowdata['departure_time'])) != $wocheheute){
        if((abs($tagabfahrt - $tagheute) == 1) and ($wocheabfahrt == $wocheheute) and $morgenproblem == 0){
            $jsonrowdata['departure_time'] = "morgen ".date("H:i",$jsonrowdata['departure_time']);
        }
        else{
            $morgenproblem = 1;
            $tag[0] = "Sonntag";
            $tag[1] = "Montag";
            $tag[2] = "Dienstag";
            $tag[3] = "Mittwoch";
            $tag[4] = "Donnerstag";
            $tag[5] = "Freitag";
            $tag[6] = "Samstag";

            //$tagnummer = date("w"); // Tag ermitteln
            $jsonrowdata['departure_time'] = $tag[date("w",$jsonrowdata['departure_time'])]." ".date("H:i",$jsonrowdata['departure_time']);
        }


    }
    else
        $jsonrowdata['departure_time'] = date("H:i",$jsonrowdata['departure_time']);


    $jsonrow[] = $jsonrowdata;
    $jsonrowdata = "";
    //print_r($row);
}
$json['fahrplan'] = $jsonrow;


//Ab Hier Message auslesen
$query = "select n.text from notification n inner join ver_notification_gtfs_stops as vngs on (n.notification_id = vngs.notification_id) where vngs.stop_id IN (".$stops_id.") and NOW() BETWEEN n.valid_from and n.valid_to group by n.notification_id";
$mysql->query($query);
//echo $query;
while($row = $mysql->fetchRow())
    $notifications[] = utf8_encode(str_replace(" ","&nbsp;",$row['text']));

$json['notifications'] = $notifications;
$json['stops'] = $stop_id;

echo json_encode($json);
?>