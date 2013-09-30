<?php
header('Content-Type: text/html; charset=utf-8');
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 14.08.13
 * Time: 13:24
 * To change this template use File | Settings | File Templates.
 */

include_once("../classMySQL.php");
include_once("classObj.php");
include_once("classContent.php");
include_once("classNotification.php");
include_once("classGTFSAgency.php");
include_once("classGTFSStop.php");
include_once("classGTFSRoutes.php");
include_once("classLinie.php");

$func = $_GET['func'];

if($func == "save_notification_edit"){

    $von = substr($_POST['von'],6,4)."-".substr($_POST['von'],3,2)."-".substr($_POST['von'],0,2)." ".substr($_POST['von'],11);
    $bis = substr($_POST['bis'],6,4)."-".substr($_POST['bis'],3,2)."-".substr($_POST['bis'],0,2)." ".substr($_POST['bis'],11);
    $text = utf8_decode(rawurldecode ($_POST['text']));

    $notification = new Notification($_POST['id']);
    $notification->set_text($text);
    $notification->set_valid_from($von);
    $notification->set_valid_to($bis);
    $notification->save();
}
if($func == "get_all_routes")
    read_all_routes();
if($func == "get_all_stops")
    read_all_stops();
if($func == "get_linien_stops")
    read_linien_stops($_GET['linie']);
if($func == "get_all_stadt")
    read_all_stadt();
if($func == "get_stadt_stops")
    read_stadt_stops($_GET['stadt']);
if($func == "save_notification")
    save_notification($_POST['von'],$_POST['bis'],$_POST['text'],$_POST['stops']);
if($func == "save_polyline"){
    save_polyline($_POST['geometries']);
}

function save_notification($von,$bis,$text,$stops){
    $von = substr($von,6,4)."-".substr($von,3,2)."-".substr($von,0,2)." ".substr($von,11);
    $bis = substr($bis,6,4)."-".substr($bis,3,2)."-".substr($bis,0,2)." ".substr($bis,11);
    $text = utf8_decode(rawurldecode ($text));
    $mysql = new MySQL();
    $mysql->query("insert into notification (text,valid_from,valid_to) values('".$text."','".$von."','".$bis."')");
    $notification_id = $mysql->last_insert_id;

    foreach($stops as $stop){
        $querystring[] = "(".$stop.",".$notification_id.")";
    }
    $querystring = implode(",",$querystring);
    $mysql->query("insert into ver_notification_gtfs_stops (stop_id,notification_id) values ".$querystring.";");
}

function read_stadt_stops($stadt){
    $mysql = new MySQL();
    $mysql2 = new MySQL();
    $mysql->query('select stop_id id,stop_name name,location_type,parent_station from gtfs_stops where stop_name LIKE "'.$stadt.'%" and location_type = 0 order by stop_name ,stop_id');
    while($row = $mysql->fetchRow()){
        if($row['location_type'] == 0){
            $mysql2->query("select r.route_short_name, t.trip_headsign from gtfs_stops s inner join gtfs_stop_times as st on (s.stop_id = st.stop_id) inner join gtfs_trips as t on (t.trip_id = st.trip_id) inner join gtfs_routes as r on (r.route_id = t.route_id) where s.stop_id = ".$row['id']." group by t.trip_headsign ,r.route_short_name order by CAST(route_short_name AS UNSIGNED) ,trip_headsign");
            $ziele = Array();
            while($row2 = $mysql2->fetchRow()){
                $ziele[] = utf8_encode($row2['route_short_name']." - ".$row2['trip_headsign']);
            }
            $row['ziele'] = $ziele;
        }
        else{
            $mysql2->query("select s.stop_id from gtfs_stops s where s.stop_id = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$row['id']." ) or s.parent_station = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$row['id']." and s2.parent_station != 0) or s.parent_station = ".$row['id']."");
            $stops_id = array();
            while($row2 = $mysql2->fetchRow())
                $stops_id[] = $row2['stop_id'];

            $stops_id = implode(',',$stops_id);

            $mysql2->query("select r.route_short_name, t.trip_headsign from gtfs_stops s inner join gtfs_stop_times as st on (s.stop_id = st.stop_id) inner join gtfs_trips as t on (t.trip_id = st.trip_id) inner join gtfs_routes as r on (r.route_id = t.route_id) where s.stop_id IN (".$stops_id.") group by t.trip_headsign ,r.route_short_name order by CAST(route_short_name AS UNSIGNED) ,trip_headsign");

            $ziele = Array();
            while($row2 = $mysql2->fetchRow()){
                $ziele[] = utf8_encode($row2['route_short_name']." - ".$row2['trip_headsign']);
            }
            $row['ziele'] = $ziele;
        }
        //$row['name'] = str_replace($stadt." ","",$row['name']);
        $row['name'] = utf8_encode(html_entity_decode($row['name']));
        $data[$row['id']] = $row;
    }
    echo json_encode($data);
}

function read_all_stadt(){
    $mysql = new MySQL();
    $mysql->query('select s.stop_name name from gtfs_stops s where s.location_type = 1 order by s.stop_name');
    $data = Array();
    while($row = $mysql->fetchRow()){
        $stadt = explode(" ",$row['name']);
        $stadt = utf8_encode($stadt[0]);
        if(!in_array($stadt,$data))
            $data[] = $stadt;
    }

    echo json_encode($data);
}

function read_all_routes(){
    $mysql = new MySQL();
    $mysql->query('select r.route_id id,r.route_short_name linie,a.agency_name agency from gtfs_routes r inner join gtfs_agency as a on (a.agency_id = r.agency_id) order by agency_name,route_short_name');
    while($row = $mysql->fetchRow())
        $data[] = $row;

    echo json_encode($data);
}

function read_all_stops(){
    $mysql = new MySQL();
    $mysql2 = new MySQL();
    $mysql->query('select stop_id id,stop_name name,location_type,parent_station from gtfs_stops where location_type = 0 order by stop_name ,stop_id');
    while($row = $mysql->fetchRow()){
        if($row['location_type'] == 0){
            $mysql2->query("select r.route_short_name, t.trip_headsign from gtfs_stops s inner join gtfs_stop_times as st on (s.stop_id = st.stop_id) inner join gtfs_trips as t on (t.trip_id = st.trip_id) inner join gtfs_routes as r on (r.route_id = t.route_id) where s.stop_id = ".$row['id']." group by t.trip_headsign ,r.route_short_name order by CAST(route_short_name AS UNSIGNED) ,trip_headsign");
            $ziele = Array();
            while($row2 = $mysql2->fetchRow()){
                $ziele[] = utf8_encode($row2['route_short_name']." - ".$row2['trip_headsign']);
            }
            $row['ziele'] = $ziele;
        }
        else{
            $mysql2->query("select s.stop_id from gtfs_stops s where s.stop_id = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$row['id']." ) or s.parent_station = (select s2.parent_station from gtfs_stops s2 where s2.stop_id = ".$row['id']." and s2.parent_station != 0) or s.parent_station = ".$row['id']."");
            $stops_id = array();
            while($row2 = $mysql2->fetchRow())
                $stops_id[] = $row2['stop_id'];

            $stops_id = implode(',',$stops_id);

            $mysql2->query("select r.route_short_name, t.trip_headsign from gtfs_stops s inner join gtfs_stop_times as st on (s.stop_id = st.stop_id) inner join gtfs_trips as t on (t.trip_id = st.trip_id) inner join gtfs_routes as r on (r.route_id = t.route_id) where s.stop_id IN (".$stops_id.") group by t.trip_headsign ,r.route_short_name order by CAST(route_short_name AS UNSIGNED) ,trip_headsign");

            $ziele = Array();
            while($row2 = $mysql2->fetchRow()){
                $ziele[] = utf8_encode($row2['route_short_name']." - ".$row2['trip_headsign']);
            }
            $row['ziele'] = $ziele;
        }
        $row['name'] = utf8_encode(html_entity_decode($row['name']));
        $data[$row['id']] = $row;
    }
    echo json_encode($data);
}

function read_linien_stops($linie){
    $mysql = new MySQL();
    $mysql2 = new MySQL();
    $mysql->query('select s.stop_id id,s.stop_name name,s.location_type,s.parent_station,t.direction_id direction from gtfs_stops s inner join gtfs_stop_times as st on (s.stop_id = st.stop_id) inner join gtfs_trips as t on (t.trip_id = st.trip_id) where t.route_id = '.$linie.'  group by s.stop_id order by t.direction_id,st.stop_sequence');
    while($row = $mysql->fetchRow()){
        if($row['location_type'] == 0){
            $mysql2->query("select r.route_short_name, t.trip_headsign from gtfs_stops s inner join gtfs_stop_times as st on (s.stop_id = st.stop_id) inner join gtfs_trips as t on (t.trip_id = st.trip_id) inner join gtfs_routes as r on (r.route_id = t.route_id) where s.stop_id = ".$row['id']." group by t.trip_headsign ,r.route_short_name order by CAST(route_short_name AS UNSIGNED) ,trip_headsign");
            $ziele = Array();
            while($row2 = $mysql2->fetchRow()){
                $ziele[] = utf8_encode($row2['route_short_name']." - ".$row2['trip_headsign']);
            }
            $row['ziele'] = $ziele;
        }
        $row['name'] = utf8_encode(html_entity_decode($row['name']));
        $data[$row['id']] = $row;
    }
    echo json_encode($data);
}

function save_polyline($geometries){
    foreach($geometries as $geo){
        if($geo['type'] == "marker"){
            $linie = new Linie($geo['id']);
            $linie->save_new_icon_posiion($geo['latlng'][1],$geo['latlng'][0]);
        }
        if($geo['type'] == "polyline"){
            $linie = new Linie($geo['id']);
            $linie->save_new_verlauf($geo['latlng']);
        }
    }
}