<?php
header('Content-Type: text/html; charset=iso-8859-1'); 
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
	UNIX_TIMESTAMP(CONCAT_WS(' ',d.day,d.time)) departure_time,
	d.destination,
	d.trip_id,
	d.route
from
	departures d
where
    d.stop_id in (".$stop_id.")
    and CONCAT_WS(' ',d.day,d.time) > NOW()
    and (select gst2.stop_id from gtfs_stop_times gst2 where gst2.trip_id = d.trip_id order by gst2.stop_sequence desc limit 1) not in (".$stop_id.")
	order by day,time,route,destination
limit ".$limitstart.",".$rows."
					";
//echo $query;
$mysql->query($query);


while($row = $mysql->fetchRow()){
    $jsonrowdata['trip_id'] = $row['trip_id'];
    $jsonrowdata['route_short_name'] = utf8_encode($row['route']);
    $jsonrowdata['trip_headsign'] = utf8_encode(str_replace($stadt." ","",$row['destination']));
    $jsonrowdata['departure_time'] = $row['departure_time'];
    $abfahrt = ceil(($jsonrowdata['departure_time'] - time())/60);
    if($abfahrt < 2)
        $jsonrowdata['departure_time'] = "sofort";
    else if($abfahrt > 60)
        $jsonrowdata['departure_time'] = date("H:i",$jsonrowdata['departure_time']);
    else
        $jsonrowdata['departure_time'] = $abfahrt."&nbsp;min";
	$jsonrow[] = $jsonrowdata;
    $jsonrowdata = "";
	//print_r($row);
}
$json['fahrplan'] = $jsonrow;


//Ab Hier Message auslesen
$query = "select n.notification from notification n inner join ver_notification_gtfs_stops as vngs on (n.notification_id = vngs.notification_id) where vngs.stop_id IN (".$stops_id.") and NOW() BETWEEN n.valite_from and n.valite_to group by n.notification_id";
$mysql->query($query);
//echo $query;
while($row = $mysql->fetchRow())
    $notifications[] = utf8_encode(str_replace(" ","&nbsp;",$row['notification']));

$json['notifications'] = $notifications;

echo json_encode($json);
?>