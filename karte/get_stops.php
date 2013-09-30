<?php
/**
 * Created by JetBrains PhpStorm.
 * User: johannes
 * Date: 11.07.13
 * Time: 22:21
 * To change this template use File | Settings | File Templates.
 */

header('Content-Type: text/html; charset=iso-8859-1');
include_once("../classMySQL.php");
$mysql = new MySQL();
$mysql2 = new MySQL();

$northEast['lat'] = $_GET['nElat'];
$northEast['lng'] = $_GET['nElng'];
$southWest['lat'] = $_GET['sWlat'];
$southWest['lng'] = $_GET['sWlng'];
//echo "select stop_id,stop_name,stop_lat,stop_lon from gtfs_stops where stop_lat between ".$southWest['lat']." and ".$northEast['lat']." and stop_lon between ".$southWest['lng']." and ".$northEast['lng']."";
$mysql->query("select stop_id,stop_name,stop_lat,stop_lon from gtfs_stops where stop_lat between ".$southWest['lat']." and ".$northEast['lat']." and stop_lon between ".$southWest['lng']." and ".$northEast['lng']." and location_type = 0");
$data = Array();
while($row = $mysql->fetchRow()){
    $data['id'] = $row['stop_id'];
    $data['name'] = utf8_encode($row['stop_name']);
    $data['lat'] = $row['stop_lat'];
    $data['lng'] = $row['stop_lon'];
    $json[] = $data;
    $data = Array();
}
echo json_encode($json);