<?php
header('Content-Type: text/html; charset=utf-8');
include_once("../classMySQL.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Notifications</title>
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
                <td style="min-width: 150px;max-width:324px;" id="uhrzeit"><?php echo date("H:i:s",time()+3600);?></td>
                <td align="center" width="95%"><h1>Noifications</h1></td>
                <td style="min-width:200px;max-width:324px;"align="right"><img src="../abfahrtsmonitor/images/swwv-logo.png" width="100%" /></td>
            </tr>
        </table>
        <a href="neue_notification.php">[ Neue Notification hinzuf&uuml;gen ]</a>
        <h2>Aktuelle</h2>
        <table border="1" style="border-style: solid;border-width: 1px;">
            <tr>
                <th>Von</th>
                <th>Bis</th>
                <th width="95%">Text</th>
            </tr>
            <?php
                $mysql = new MySQL();
                $query = "select notification_id,notification,DATE_FORMAT(valite_from,'%d.%m.%Y %h:%i')valite_from,DATE_FORMAT(valite_to,'%d.%m.%Y %h:%i')valite_to from notification where valite_from < NOW() and valite_to > NOW()";
                $mysql->query($query);
                while($row = $mysql->fetchRow()){
                    echo "<tr>";
                    echo "<td>";
                    echo str_replace(" ","&nbsp;",$row['valite_from']);
                    echo "</td>";
                    echo "<td>";
                    echo str_replace(" ","&nbsp;",$row['valite_to']);
                    echo "</td>";
                    echo "<td>";
                    echo $row['notification'];
                    echo "</td>";
                    echo "<td>";
                    echo "<a href='details_notification.php?id=".$row['notification_id']."'>[&nbsp;details&nbsp;]</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            ?>
        </table>
        <h2>Zuk&uuml;nftige</h2>
        <table>
            <tr>
                <th>Von</th>
                <th>Bis</th>
                <th>Text</th>
            </tr>
            <?php
            $mysql = new MySQL();
            $query = "select notification_id,notification,DATE_FORMAT(valite_from,'%d.%m.%Y %h:%i')valite_from,DATE_FORMAT(valite_to,'%d.%m.%Y %h:%i')valite_to from notification where valite_from > NOW()";
            $mysql->query($query);
            while($row = $mysql->fetchRow()){
                echo "<tr>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$row['valite_from']);
                echo "</td>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$row['valite_to']);
                echo "</td>";
                echo "<td>";
                echo $row['notification'];
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
        <h2>Alte</h2>
        <table>
            <tr>
                <th>Von</th>
                <th>Bis</th>
                <th>Text</th>
            </tr>
            <?php
            $mysql = new MySQL();
            $query = "select notification_id,notification,DATE_FORMAT(valite_from,'%d.%m.%Y %h:%i')valite_from,DATE_FORMAT(valite_to,'%d.%m.%Y %h:%i')valite_to from notification where valite_to < NOW()";
            $mysql->query($query);
            while($row = $mysql->fetchRow()){
                echo "<tr>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$row['valite_from']);
                echo "</td>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$row['valite_to']);
                echo "</td>";
                echo "<td>";
                echo $row['notification'];
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>


    </div>
</div>
</body>
</html>
