<?php
header('Content-Type: text/html; charset=utf-8');
include_once("../classMySQL.php");
$mysql = new MySQL();
$query = "select notification,DATE_FORMAT(valite_from,'%d.%m.%Y %h:%i')valite_from,DATE_FORMAT(valite_to,'%d.%m.%Y %h:%i')valite_to from notification where notification_id = ".$_GET['id'];
$mysql->query($query);
$row = $mysql->fetchRow();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Notification bearbeiten</title>
    <script src="../js/jquery-1.9.1.min.js"></script>
    <script src="../js/jquery.marquee.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="../faviconswwv.ico" />
    <link type="text/css" href="../style.css" rel="stylesheet">
    <script language="javascript">
        function uhrzeit(){
            time = new Date();
            var stunde = time.getHours();
            var minute = time.getMinutes();
            var sekunde = time.getSeconds();
            if(stunde < 10)
                stunde = "0" + stunde;
            if(minute < 10)
                minute = "0" + minute;
            if(sekunde < 10)
                sekunde = "0" + sekunde;
            document.getElementById('uhrzeit').innerHTML =  stunde + ":" + minute + ":" + sekunde;
        }
        setInterval("uhrzeit()",100);
    </script>
</head>

<body>
<div id="wrapper">
    <div style="height 100% ;margin: 10px 10px 10px 10px;" id="content">
        <table border="0" style=" max-height:80px;width: 100%;">
            <tr>
                <td style="min-width: 150px;max-width:324px;" id="uhrzeit"><?php echo date("H:i:s");?></td>
                <td align="center" width="95%"><h1>Noification bearbeiten</h1></td>
                <td style="min-width:200px;max-width:324px;"align="right"><img src="../abfahrtsmonitor/images/swwv-logo.png" width="100%" /></td>
            </tr>
        </table>
        <table>
            <tr>
                <th>Gültig von</th>
                <td><?php echo $row['valite_from'];?></td>
            </tr>
            <tr>
                <th>Gültig bis</th>
                <td><?php echo $row['valite_to'];?></td>
            </tr>
            <tr>
                <th>Text</th>
                <td><?php echo $row['notification'];?></td>
            </tr>
            <tr valign="top">
                <th>Haltestellen</th>
                <td>
                    <?php
                        $stops = get_stops($_GET['id']);
                        foreach($stops as $stop)
                            echo $stop['name']."<br>";
                    ?>
                </td>
            </tr>
        </table>

    </div>
</div>
</body>
</html>
<?php
function get_stops($notification_id){
    $mysql = new MySQL();
    $query = "select gs.stop_id,gs.stop_name from gtfs_stops gs inner join ver_notification_gtfs_stops as vngs on (vngs.stop_id = gs.stop_id) where vngs.notification_id = ".$notification_id;
    $mysql->query($query);
    while($row = $mysql->fetchRow()){
        $stops[] = Array("id" => $row['stop_id'],"name" => utf8_encode($row['stop_name']));
    }
    return $stops;
}
?>