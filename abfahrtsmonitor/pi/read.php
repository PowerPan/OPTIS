<?php
header('Content-Type: text/html; charset=utf8');
date_default_timezone_set("Europe/Berlin");
include_once("classMySQL.php");
$mysql = new MySQL();

$type = $_GET['type'];

if($type == "settings"){
    $mysql->query("select stopname,textsize,start,scrollamount,stop_ids from settings");
    $row = $mysql->fetchRow();
    echo json_encode($row);
}
if($type == "departures"){
    $rows = $_GET['rows'];
    $limitstart = 0;
    $stadt = "Wilhelmshaven";


    $query="SELECT
        UNIX_TIMESTAMP(d.date) departure_time,
        d.destination,
        d.route,
        d.infotext
    from
        departures d
    where
        d.date > NOW()
    group by departure_time,destination,route
    order by departure_time,route,destination
    limit ".$rows."
                        ";
    //echo $query;
    $mysql->query($query);

    $tagheute = date('N');
    $wocheheute = date("W");
    $morgenproblem=0;
    while($row = $mysql->fetchRow()){
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
    $query = "select text from notification where NOW() BETWEEN valid_from and valid_to";
    $mysql->query($query);
    //echo $query;
    while($row = $mysql->fetchRow())
        $notifications[] = utf8_encode(str_replace(" ","&nbsp;",$row['text']));

    $json['notifications'] = $notifications;

    echo json_encode($json);

}
?>