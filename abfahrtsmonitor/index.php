<?php
header('Content-Type: text/html; charset=utf8');
include_once("../classMySQL.php");
if(isset($_GET['stop_id']))
	$stop_id = $_GET['stop_id'];
else
	$stop_id = "425";
$mysql = new MySQL();
$mysql->query("select stop_name from gtfs_stops where stop_id = '".$stop_id."'");
$row = $mysql->fetchRow();
$rows = $_GET['rows'];
if($rows == "")
	$rows = 5;
if(isset($_GET['headeroff']))
    $header = false;
else
    $header = true;

if(isset($_GET['firstrowoff']))
    $firstrow = false;
else
    $firstrow = true;

if(isset($_GET['overflowoff']))
    $overflow = false;
else
    $overflow = true;

//Für 2 Monitor dadrunter oder daneben um nicht die ersten abfahretn zu bekommen sondern einen Versatz
if(isset($_GET['start'])){
    $start = $_GET['start'];
    $start -= 2;
    $rows++;
}
else
    $start = 0;

if(isset($_GET['headersize']))
    $headersize = $_GET['headersize'];
else
    $headersize = 50;

if(isset($_GET['textsize']))
    $textsize = $_GET['textsize'];
else
    $textsize = 40;

if(isset($_GET['notificationoff']))
    $notification = false;
else
    $notification = true;

if(isset($_GET['notificationsize']))
    $notificationsize= $_GET['notificationsize'];
else
    $textsize = 40;

if(isset($_GET['scrollamount']))
    $scrollamount= $_GET['scrollamount'];
else
    $scrollamount = 4;

if(isset($_GET['fahrplanpadding']))
    $fahrplanpadding= $_GET['fahrplanpadding'];
else
    $fahrplanpadding = 0;



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF8" />
<title><?php echo str_replace("Wilhelmshaven ","",$row['stop_name']); ?> - Abfahrtsmonitor</title>
<script src="js/jquery-1.9.1.min.js"></script>
<script src="js/jquery.marquee.js"></script>
<link rel="shortcut icon" type="image/x-icon" href="../faviconswwv.ico" />
<link type="text/css" href="../style.css" rel="stylesheet">
<style type="text/css">
body {
    font-size: <?php echo $textsize;?>px;
    <?php if($overflow) {?>
    overflow: hidden;
    <?php } ?>

}
h1{
	font-size:<?php echo $headersize;?>px;
}

th{
	font-size:<?php echo $textsize;?>px;
}

.fahrplanzeile{
    height: <?php echo $textsize*2 + 4;?>px;
    word-wrap:break-word;
}



#wrapper {
    top: 0px;
}
</style>
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
        $("#content").css({"height": window.innerHeight+"px"});
	}

	setInterval("uhrzeit()",100);
    $(document).ready(function() {

    });

    var notification_queue;
	var notification_row = false;
	function read_fahrplan(){
		$.post('read_haltestellenplan.php?stop_id=<?php echo $stop_id; ?>&rows=<?php echo $rows; ?>&limitstart=<?php echo $start; ?>',function(data) {
			var fahrplanObj = JSON.parse(data);
            var rows = <?php
                if(isset($_GET['start'])) //Zweiter Bildschirm
                    echo $rows-1;
                else
                    echo $rows;
                ?>;
            var startrow;
            if(fahrplanObj.notifications){
                rows--;
                startrow = 0;
                <?php
                    if(isset($_GET['start'])){ //Zweiter Bildschirm
                        ?>
                            //$("#fahrplan_zeile_"+i).remove();
                        <?php
                    }
                ?>
            }
            else{
                <?php
                if(isset($_GET['start'])) //Zweiter Bildschirm
                    echo "startrow = 1;";
                else
                    echo "startrow = 0;";
                ?>;

            }


            for(i = 0, j = startrow ; i < rows ; i++, j++)
                $("#fahrplan_zeile_"+i).html("<td class='zeile_linie'>"+ fahrplanObj.fahrplan[j].route_short_name +"</td><td class='zeile_ziel'>"+ fahrplanObj.fahrplan[j].trip_headsign + "<br><div class='infotext'>"+ fahrplanObj.fahrplan[j].infotext+"</div></td><td class='zeile_abfahrt' valign='top'>"+ fahrplanObj.fahrplan[j].departure_time + "</td>");

            if(!notification_row && fahrplanObj.notifications){
                $("#fahrplan_zeile_"+rows).html("<td valign='middle' colspan='3' style='max-width: "+((screen.width)-60)+"px; vertical-align: middle;'><marquee scrollamount='<?php echo $scrollamount;?>'><div id='notification_marquee'>&nbsp;</div></marquee></td>");
                notification_row = true;
            }

            if(fahrplanObj.notifications){
                var notification = new Array();
                for(i = 0;i < fahrplanObj.notifications.length;i++)
                    notification.push(fahrplanObj.notifications[i]);
                notification_queue = notification.join('&nbsp;+++&nbsp;');
                $("#notification_marquee").html(notification_queue);
            }

            if(notification_row && !fahrplanObj.notifications)
                notification_row = false;

			setTimeout("read_fahrplan()",5000);
        })
        .fail(function() { setTimeout("read_fahrplan()",10000); });
	}
	setTimeout("read_fahrplan()",100);

</script>
</head>

<body>
<div id="wrapper">
<div style="margin: 10px 10px 10px 10px;" id="content">
    <?php
        if($header){
    ?>
<table border="0" style=" max-height:80px;width: 100%;  margin-bottom: <?php echo 20+($fahrplanpadding/2);?>px; margin-top: <?php echo ($fahrplanpadding/2);?>px">
	<tr>
        <td style="min-width:300px;max-width:300px;"><img src="../images/swwvvej.png" width="100%" /></td>
        <td align="center" width="95%"><h1><?php echo str_replace("Wilhelmshaven ","",$row['stop_name']); ?></h1></td>
        <td style="min-width: 130px;max-width:130px; text-align: right" >&nbsp;</td>
        <td style="min-width: 170px;max-width:170px; text-align: left" id="uhrzeit"><?php echo date("H:i:s");?></td>
  	</tr>
</table>
    <?php
        }


    echo "<div id='fahrplan' >";
    echo "<table border='0' class='tabellefahrplan'>";
    echo "<tr class='headerzeile'><td class='header_zeile_linie'>Linie</td><td class='header_zeile_ziel'>Ziel</td><td class='header_zeile_abfahrt'>Abfahrt</td></tr>";
    for($i = 0;$i < $rows;$i++)
        echo "<tr id='fahrplan_zeile_$i' class='fahrplanzeile'><td class='zeile_linie'>&nbsp;</td><td class='zeile_ziel'>&nbsp;</td><td class='zeile_abfahrt' valign='top'>&nbsp;</td></tr>";
    echo "</table>";
	?>

</div>
</div>
</body>
</html>