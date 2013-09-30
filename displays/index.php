<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 23.09.13
 * Time: 13:24
 * To change this template use File | Settings | File Templates.
 */
include_once('../classMySQL.php');
include_once('../admin/classObj.php');
include_once('../admin/classDisplay.php');
include_once('../admin/classGTFSStop.php');
if(isset($_GET['mac'])){
    $mac = $_GET['mac'];
    $action = $_GET['action'];
    $json = Array();

 $display = new Display();
    $display->read_with_mac($mac);
    $display->heartbeat();

        $mysql = new MySQL();

        if($action == "settings"){
            $json['textsize'] = $display->get_textsize();
            $json['start'] = $display->get_start();
            $json['scrollamount'] = $display->get_scrollamount();
            $json['stopname'] = $display->get_stopname();
        }

        if($action == "departures"){
            foreach ($display->get_stops() as $stop){
                $stops[] = $stop->get_id();
            }
            $query = "select date,destination,route,trip_id,infotext from departures where stop_id in (".implode(',',$stops).") and date > NOW()";
            $mysql->query($query);
            while($row = $mysql->fetchRow()){
                $json['departures'][] = $row;
            }
        }

        if($action == "notifications"){
            if ($display->get_notifications()){
                foreach ($display->get_stops() as $stop){
                    $stops[] = $stop->get_id();
                }
                $query = "select n.notification_id,n.text,n.valid_from,n.valid_to from notification n left join ver_notification_gtfs_stops as vns on (vns.notification_id = n.notification_id) where vns.stop_id in (".implode(',',$stops).") and valid_to > NOW() group by n.notification_id ";
                $mysql->query($query);
                while($row = $mysql->fetchRow()){
                    $json['notifications'][] = $row;
                }
            }
        }

        echo json_encode($json);
}

?>




