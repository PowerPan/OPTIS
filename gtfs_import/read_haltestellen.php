<?php
/*
*   Hier werden die Haltestellen für die Karte aus der Datenbank gelsen und
*   in die Datei haltestellen.js gespeichert, welche für die Darstellung der
*   Haltestellen auf der Karte benötigt wird
*
*   Die Daten werden in JSON Notation in die Datei geschrieben. Es werden
*   2 JSON Objekte geschrieben. Das JSON Objekt "haltestellenObj" beinhaltet alle
*   Einzelhaltestellen bzw man könnte auch sagen jedes Haltestellenschild
*
*   Das JSON Objekt "haltestellenGroupObj" behinhaltet alle Gruppierten Haltestellen.
*   Diese werden benötigt um es bei einem kleinen Zommfaktor die übersichtlichkeit der Karte zu gewährleisten.
*/
header('Content-Type: text/html; charset=iso-8859-1');
set_time_limit(5000);
include_once("classMySQL.php");
$mysql = new MySQL();
$mysql2 = new MySQL();
$mysql3 = new MySQL();
$mysql4 = new MySQL();
$datei = fopen("../karte/haltestellen.js",w);
//Ein SQL Query für die Haltestellen und eine für die gruppierten Haltestellen.
$query_haltestellen="select
					stop_id id
					,stop_name name
					,stop_lat lat
					,stop_lon lng
				from
					gtfs_stops
				where
				    location_type = 0

				order by stop_name
				"			;
$query_haltestellengroup="select
					stop_id id
					,stop_name name
					,stop_lat lat
					,stop_lon lng
				from
					gtfs_stops
				where
				    location_type = 1
				order by stop_name

				";



//Auslesen und Erstellen des Haltestellen Objektes
$mysql->query($query_haltestellen);
$counter_haltestellen = $mysql->count();
$haltestellen = "var haltestellenObj = {";
$haltestellen .= "\"haltestellen\": [";
while($row = $mysql->fetchRow()){
    //Auslesen der Stops doe keine Endhaltestelle sein dürfen
    $query_stops_id_gruppe = "select s.stop_id from gtfs_stops s where s.stop_id  = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$row['id']." ) or s.parent_station = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$row['id']." ) ";
    $mysql4->query($query_stops_id_gruppe);
    while($row4 = $mysql4->fetchRow())
        $stop_ids_gruppe[] = $row4['stop_id'];

    $stop_ids_gruppe = implode(",",$stop_ids_gruppe);


    $tempstring = '{ "id": "'.$row['id'].'","name": "'.str_replace("Wilhelmshaven ","",$row['name']).'","lat": "'.$row['lat'].'","lng": "'.$row['lng'].'","linien" : [ ';


    $query_linien = "  select
                            r.route_id
                            ,r.route_short_name
                            ,t.trip_headsign
                        from
                            gtfs_routes r
                            left join gtfs_trips as t on (r.`route_id` = t.`route_id`)
                            left join gtfs_stop_times as st on (st.`trip_id` = t.`trip_id`)
                        where
                            st.stop_id = ".$row['id']."
                            and (select gst2.stop_id from gtfs_stop_times gst2 where gst2.trip_id = t.trip_id order by gst2.stop_sequence desc limit 1) not in (".$stop_ids_gruppe.")
                        group by
                            route_id,
                            trip_headsign";


    //echo $query_linien;
    /*
    $mysql2->query($query_linien);
    if($mysql2->count() > 0){
        while($row2 = $mysql2->fetchRow()){
            //print_r($row2);
            $linie[$row2['route_id']]['Linie'] = $row2['route_short_name'];
            $linie[$row2['route_id']]['Richtung'][] = str_replace("Wilhelmshaven ","",$row2['trip_headsign']);
            $linie[$row2['route_id']]['stop_id'] = $row['id'];
            $linie[$row2['route_id']]['route_id'] = $row2['route_id'];
        }
        //echo "Stop_id: ".$row['id']."<br>";
        //print_r($linie);
        foreach($linie as $lin){
            $tempstring2[] = '{"Linie" : "'.$lin['Linie'].'","Richtung" : "'.implode(" / ",$lin['Richtung']).'","stop_id" : "'.$lin['stop_id'].'","route_id" : "'.$lin['route_id'].'"} ';
        }

        $tempstring .= implode(" , ",$tempstring2)."]";
    }
    else {
        $tempstring .= "]";
    }*/
    $tempstring .= "]";
    $linie = NULL;
    $tempstring2 = NULL;
    $stop_ids_gruppe = NULL;

    $tempstring .= ' }';

    $hststring[] = $tempstring;
    //echo "<br>----------<br>";
}
$haltestellen .= implode(",",$hststring);
$haltestellen .=  "]};";
fwrite($datei,$haltestellen." ");
//echo $haltestellen;

//Auslesen und Erstellen des HaltestellenGroup Objektes
$mysql->query($query_haltestellengroup);
$counter_haltestellen_group = $mysql->count();
$haltestellengroup = "var haltestellenGroupObj = {";
$haltestellengroup .= "\"haltestellen\": [";
while($row = $mysql->fetchRow()){
    $tempstring = '{ "id": "'.$row['id'].'","name": "'.str_replace("Wilhelmshaven ","",$row['name']).'","lat": "'.$row['lat'].'","lng": "'.$row['lng'].'","linien" : [ ';

    /*$mysql2->query("select stop_id from gtfs_stops where parent_station = ".$row['id']);
    while($row2 = $mysql2->fetchRow()){
        $mysql4->query("select s.stop_id from gtfs_stops s where s.stop_id  = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$row['id']." ) or s.parent_station = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$row['id']." ) ");
        while($row4 = $mysql4->fetchRow())
            $stop_ids_gruppe[] = $row4['stop_id'];

        $stop_ids_gruppe = implode(",",$stop_ids_gruppe);


        $query_linien = "  select
                            r.route_id
                            ,r.route_short_name
                            ,t.trip_headsign
                        from
                            gtfs_routes r
                            left join gtfs_trips as t on (r.`route_id` = t.`route_id`)
                            left join gtfs_stop_times as st on (st.`trip_id` = t.`trip_id`)
                        where
                            st.stop_id = ".$row2['stop_id']."
                            and (select gst2.stop_id from gtfs_stop_times gst2 where gst2.trip_id = t.trip_id order by gst2.stop_sequence desc limit 1) not in (".$stop_ids_gruppe.")
                        group by
                            route_id,
                            trip_headsign";

        $mysql3->query($query_linien);
        while($row3 = $mysql3->fetchRow()){
            $linie[$row3['route_id']]['Linie'] = $row3['route_short_name'];
            $linie[$row3['route_id']]['Richtung'][] = str_replace("Wilhelmshaven ","",$row3['trip_headsign']);
            $linie[$row3['route_id']]['stop_id'] = $row2['stop_id'];
            $linie[$row3['route_id']]['route_id'] = $row3['route_id'];
        }

        foreach($linie as $lin){
            $tempstring2[] = '{"Linie" : "'.$lin['Linie'].'","Richtung" : "'.implode(" / ",$lin['Richtung']).'","stop_id" : "'.$lin['stop_id'].'","route_id" : "'.$lin['route_id'].'"} ';
        }

        $stop_ids_gruppe = NULL;
        $linie = NULL;



    }

    $tempstring .= implode(" , ",$tempstring2)."]}";*/
    $tempstring .= "]}";
    $tempstring2 = NULL;
    $hstgroupstring[] = $tempstring;

}
$haltestellengroup .= implode(",",$hstgroupstring);
$haltestellengroup .=  "]};";

//echo $haltestellengroup;


fwrite($datei,$haltestellengroup);
fclose($datei);
echo $counter_haltestellen;
echo " Haltestellen und ";
echo $counter_haltestellen_group;
echo " Gruppenhaltestellen für die Karte ausgelesen";

?>