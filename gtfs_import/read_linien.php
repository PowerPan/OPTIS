<?php
/* Skript zum auslesen der linien verläufe */
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../classMySQL.php");
$mysql = new MySQL();
$mysql2 = new MySQL();
$mysql->query("select route_id,farbe,name,icon_lat,icon_lng,icon,icon_x,icon_y from linien order by name");
$string = 'var linienObj = { "linien": [';
$count_linien = $mysql->count();
while($row = $mysql->fetchRow()){
	$linienstring = '{ "id": "'.$row['route_id'].'", "name": "'.$row['name'].'", "color": "'.$row['farbe'].'", "lat": "'.$row['icon_lat'].'", "lng": "'.$row['icon_lng'].'", "icon": "'.$row['icon'].'", "latlng": [ ';
	$mysql2->query("select lat,lng,sort from linien_verlauf where route_id = ".$row['route_id']." order by sort");
	while($row2 = $mysql2->fetchRow()){
		$latlngstring[] = '{ "lat" : "'.$row2['lat'].'" , "lng" : "'.$row2['lng'].'" }';	
	}
	$linienstring .= implode(",",$latlngstring);
	$latlngstring = NULL;
	$linienstring .= ']}';
	$linienstringarray[] = $linienstring;
}
$string .= implode(",",$linienstringarray);
$string .= ']};';


$datei = fopen("../karte/linien.js",w);
fwrite($datei,$string);
fclose($datei);
echo $count_linien;
echo " Linien verläufe für die Karte ausgelesen"



				?>
