<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 24.09.13
 * Time: 11:45
 * To change this template use File | Settings | File Templates.
 */

include_once("classMySQL.php");
$mysql = new MySQL();
$mysql->query("select stopname,textsize,start,scrollamount,stop_ids from settings");
$row = $mysql->fetchRow();
$stopname = $row['stopname'];
$textsize = $row['textsize'];
$start = $row['start'];
$scrollamount = $row['scrollamount'];
$stop_ids = $row['stop_ids'];

$zeilenhoehe = 2*$textsize + 6;


?>
<html>
    <head>
        <script src="jquery-2.0.3.min.js"></script>
        <style type="text/css">
            body{
                margin: 0;
            }

            html{
                background-color: #276da1;
                color: white;
                font-family: arial;
                font-size: <?php echo $textsize;?>px;
            }



            #headerlogo{
                width: 300px;
            }

            #headerlogo img{
                width: inherit;
            }

            #headeruhrzeit{
                width: 300px;
            }
            #headeruhrzeit > div {
                float: right;
            }


            #headername{
                text-align: center;
                font-size: <?php echo 1*$textsize;?>px;
            }

            #headertable{
                margin-left: 20px;
                margin-right: 20px;
                margin-top: 20px;
                margin-bottom: 20px;
                font-size: inherit;
            }




            #fahrplantable{
                width: 100%;
                border-collapse: collapse;
                font-size: inherit;
            }

            #fahrplantable_linie{
                border-bottom: 3pt solid white;
                border-top: 3pt solid white;
                width: 150px;
                text-indent: 20px;
                text-align: left;
            }

            #fahrplantable_ziel{
                border-bottom: 3pt solid white;
                border-top: 3pt solid white;
                text-align: left;
                text-indent: 20px;
            }

            #fahrplantable_abfahrt{
                border-bottom: 3pt solid white;
                border-top: 3pt solid white;
                text-align: left;
                text-indent: 20px;
                padding-right: 20px;
                width: 150px;
            }




            .departurerow_linie{
                border-bottom: 1pt solid white;
                width: 150px;
                text-indent: 20px;
            }

            .departurerow_ziel{
                border-bottom: 1pt solid white;
                text-indent: 20px;
            }

            .departurerow_abfahrt{
                border-bottom: 1pt solid white;
                width: 150px;
                text-indent: 20px;
                font-size: inherit;
                padding-right: 20px;
            }

            .departurerow{
                height: <?php echo $zeilenhoehe;?>px;
                font-size: inherit;
            }

            .infotext_ziel{
                font-size: 0.5em;
                color: gold;
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
                $("#uhrzeit").html(stunde + ":" + minute + ":" + sekunde);

            }



            var textsize = <?php echo $textsize;?>;
            var zeilen;

            //Breite des Anzeigebereiches
            var breite = window.innerWidth;

            //Höhe des Anzeigebereiches
            var hoehe = window.innerHeight;

            $( document ).ready(function() {
                $("#uhrzeit").html("88:88:88");
                var uhrzeitwidth = document.getElementById('uhrzeit').offsetWidth;
                $("#uhrzeit").css("width",uhrzeitwidth);
                setInterval("uhrzeit()",100);

                //hoehezeilen = Platz, der für die Abfahrtzeilen vorhanden  ist
                var hoehezeilen = hoehe;

                //Breite der Header Tabelle = Breite - Rand Links und Rechts
                var breiteheadertable = breite - 40;
                $("#headertable").css("width",breiteheadertable+"px");

                var breitename = breiteheadertable-300-300;


                var breiteheadertext = document.getElementById('headername').offsetWidth;
                var fontsize;
                while (breiteheadertext > breitename){
                    fontsize = parseInt($('#headername').css("font-size"));
                    fontsize--;
                    $('#headername').css("font-size",fontsize+"px");
                    breiteheadertext = document.getElementById('headername').offsetWidth;
                }


                //console.log(hoehezeilen);
                //Ausrechnen wie viele Zeilen passen
                var hoeheheadertable = document.getElementById('headertable').offsetHeight+20;
                var hoehefahrplanfirstrow = document.getElementById('fahrplantableheadrow').offsetHeight;
                hoehezeilen -= hoeheheadertable;
                hoehezeilen -= hoehefahrplanfirstrow;
                var zeilenhoehe = <?php echo $zeilenhoehe;?>;
                zeilen = parseInt(hoehezeilen / zeilenhoehe);



                for(var i = 1; i <= zeilen;i++){
                    var departurerow = '<tr class="departurerow">' +
                        '<td class="departurerow_linie" id="departurerow_linie_'+i+'"></td>' +
                        '<td class="departurerow_ziel" id="departurerow_ziel_'+i+'"><div class="infotext_ziel"></div></td>' +
                        '<td class="departurerow_abfahrt" id="departurerow_abfahrt_'+i+'"></td>' +
                    '</tr>';
                    $('#fahrplantable tr:last').after(departurerow);
                }


                //Die Tabelle mit dem restlichen Freiraum nach unten schieben

                var margin = hoehe - hoeheheadertable - document.getElementById('fahrplantable').offsetHeight -20;
                var margin_bottom_header_table = parseInt($('#headertable').css("margin-bottom"));
                var margin_top_header_table = parseInt($('#headertable').css("margin-top"));
                margin_bottom_header_table += (margin/2);
                margin_top_header_table += (margin/2);
                $('#headertable').css("margin-bottom",margin_bottom_header_table+"px");
                $('#headertable').css("margin-top",margin_top_header_table+"px");

                setTimeout("read_fahrplan()",1000);

            });

            var settings_stopname = "<?php echo $stopname;?>";
            var settings_textsize= "<?php echo $textsize;?>";
            var settings_start= "<?php echo $start;?>";
            var settings_scrollamount= "<?php echo $scrollamount;?>";
            var settings_stop_ids= "<?php echo $stop_ids;?>";

            function read_settings(){
                $.getJSON('read.php?type=settings',function(data) {
                    if(data.stopname != settings_stopname)
                        location.reload();
                    if(data.textsize != settings_textsize)
                        location.reload();
                    if(data.start != settings_start)
                        location.reload();
                    if(data.scrollamount != settings_scrollamount)
                        location.reload();
                    if(data.stop_ids != settings_stop_ids)
                        location.reload();

                });
            }
            setInterval('read_settings()',10000);
            read_settings();

            var notification_row = false;
            function read_fahrplan(){
                $.post('read.php?type=departures&rows='+zeilen,function(data) {
                    var fahrplanObj = JSON.parse(data);
                    var rows = zeilen;

                    if(fahrplanObj.notifications){
                        rows--;

                        var notification = new Array();
                        for(i = 0;i < fahrplanObj.notifications.length;i++)
                            notification.push(fahrplanObj.notifications[i]);
                        var notifications = notification.join('&nbsp;+++&nbsp;');
                        if(notification_row)
                            $("#notification_marquee").html(notifications);
                        else
                            $("#fahrplantable tr:last").html("<td valign='middle' colspan='3' style='max-width: "+breite+"px; vertical-align: middle;'><marquee scrollamount='<?php echo $scrollamount;?>'><div id='notification_marquee'>"+notifications+"</div></marquee></td>");
                        notification_row = true;
                    }
                    else if(notification_row){
                        var departurerow =  '<td class="departurerow_linie" id="departurerow_linie_'+zeilen+'">123</td>' +
                                            '<td class="departurerow_ziel" id="departurerow_ziel_'+zeilen+'">123<div class="infotext_ziel">123</div></td>' +
                                            '<td class="departurerow_abfahrt" id="departurerow_abfahrt_'+zeilen+'">23:32</td>';

                        $("#fahrplantable tr:last").html(departurerow);
                    }


                    for(i = 0, j = 1; i < rows ; i++,j++){
                        $("#departurerow_linie_"+j).html(fahrplanObj.fahrplan[i].route_short_name);
                        $("#departurerow_ziel_"+j).html(fahrplanObj.fahrplan[i].trip_headsign + "<div class='infotext_ziel'>"+ fahrplanObj.fahrplan[i].infotext+"</div>");
                        $("#departurerow_abfahrt_"+j).html(fahrplanObj.fahrplan[i].departure_time );
                    }


                    /*var rows = <?php
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
                        */

                    setTimeout("read_fahrplan()",5000);
                })
                    .fail(function() { setTimeout("read_fahrplan()",10000); });
            }

        </script>
    </head>
    <body>
        <table id="headertable" border="0">
            <tr>
                <td id="headerlogo"><img src="swwvvej.png"/> </td>
                <td id="headername" nowrap><?php echo $stopname;?></td>
                <td id="headeruhrzeit">
                    <div id="uhrzeit"></div>
                </td>
            </tr>
        </table>
        <table id="fahrplantable" border="0">
            <tr id="fahrplantableheadrow">
                <th id="fahrplantable_linie">Linie</th>
                <th id="fahrplantable_ziel">Ziel</th>
                <th id="fahrplantable_abfahrt">Abfahrt</th>
            </tr>
        </table>
    </body>
</html>