<?php
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../classMySQL.php");
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Neue Notification</title>
        <script src="../js/jquery-1.9.1.min.js"></script>
        <script src="../js/jquery.ui.core.js"></script>
        <script src="../js/jquery.ui.widget.js"></script>
        <script src="../js/jquery.ui.datepicker.js"></script>
        <script src="../js/jquery-ui-timepicker-addon.js"></script>
        <script src="../js/datetimepicker.settings.js"></script>
        <link rel="shortcut icon" type="image/x-icon" href="../faviconswwv.ico" />
        <link rel="stylesheet" href="../js/themes/base/jquery.ui.all.css">
        <link type="text/css" href="../style.css" rel="stylesheet">
        <script language="javascript">



            var stops;
            var save_stops = new Array();
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

            function select_haltestellen_optionen(){
                var option = $("#haltestellenoptionen").val()
                $('#linien').html("");
                $('#haltestellen').html("");
                $('#stadt').html("");
                $('#loader').css("visibility","visible");
                if(option == "null"){
                    $('#linien').html("");
                    $('#haltestellen').html("");

                }
                if(option == "all"){
                    $('#linien').html("");
                    $('#haltestellen').html("");
                    $.getJSON('ajax.php?func=get_all_stops', function(data) {
                        $('#loader').css("visibility","hidden");
                        stops = data;
                        print_stops();
                    });
                }
                if(option == "linie"){
                    $.getJSON('ajax.php?func=get_all_routes', function(data) {
                        $('#loader').css("visibility","hidden");
                        var html = '<select id="selectlinien" onchange="print_linien_stops()">';
                        html += '<option value="null" ></option>';
                        for(var i = 0; i< data.length;i++){
                            html += '<option value="'+ data[i].id +'">'+ data[i].agency +' - '+ data[i].linie +'</option>'
                        }
                        html += '</select>';
                        $('#linien').html(html);
                    });
                }
                if(option == "stadt"){
                    $.getJSON('ajax.php?func=get_all_stadt', function(data) {
                        $('#loader').css("visibility","hidden");
                        var html = '<select id="selectstadt" onchange="print_stadt_stops()">';
                        html += '<option value="null" ></option>';
                        for(var i = 0; i< data.length;i++){
                            html += '<option value="'+ data[i] +'">'+ data[i] +'</option>'
                        }
                        html += '</select>';
                        $('#stadt').html(html);
                    });
                }
            }

            function print_stadt_stops(){
                $('#loader').css("visibility","visible");
                var stadt = $("#selectstadt").val();
                $.getJSON('ajax.php?func=get_stadt_stops&stadt='+stadt, function(data) {
                    $('#loader').css("visibility","hidden");
                    stops = data;
                    print_stops();
                });
            }

            function print_linien_stops(){
                $('#loader').css("visibility","visible");
                var linien = $("#selectlinien").val();
                $.getJSON('ajax.php?func=get_linien_stops&linie='+linien, function(data) {
                    $('#loader').css("visibility","hidden");
                    stops = data;
                    print_stops();
                });
            }

            function print_stops(){
                var html = "<br/><a onclick='add_all_station_to_queue()'>-->Alle ausw&auml;len --></a><br/><br/>";
                for(var i in stops){
                    html += "<li id='"+stops[i].id+"'" ;
                    if(stops[i].location_type == 0)
                        html +="style='text-indent:0px;'";
                    html += ">" +
                        "<span class='dropt'>" +
                            "<a onclick='add_station_to_queue("+stops[i].id+")'>"+stops[i].name;
                    if(stops[i].location_type == 1)
                        html += " (G)";
                    html +=        "</a>" +
                            "<span style='width:500px;text-indent:0px;'>"+stops[i].ziele.join("<br>")+"</span>" +
                        "</span>" +
                        "</li>";
                }
                //html += "</ul>";
                $('#haltestellen').html(html);
            }

            function add_all_station_to_queue(){
                for(var i in stops){
                    add_station_to_queue(i);
                }
            }

            function add_station_to_queue(id){
                if(jQuery.inArray(id,save_stops) == -1){
                    save_stops.push(id);
                    $("#"+id).appendTo("#haltestellenselect");
                }
                else{
                    save_stops.splice(save_stops.indexOf(id),1);
                    $("#"+id).appendTo("#haltestellen");
                }
            }

            function speichern(){
                var von = $("#von").val()
                var bis = $("#bis").val()
                var text  = encodeURIComponent($("#text").val());
                $.post('ajax.php?func=save_notification',{von: von,bis: bis,text: text, stops: save_stops}, function(data) {
                    alert(data);
                });
            }



        </script>
    </head>

    <body>
    <div id="wrapper">
        <div id="content">
            <table border="0" style=" max-height:80px;width: 100%;">
                <tr>
                    <td style="min-width: 150px;max-width:324px;" id="uhrzeit" ><?php echo date("H:i:s",time()+3600);?></td>
                    <td align="center" width="95%"><h1>Neue Noification</h1></td>
                    <td style="min-width:200px;max-width:324px;"align="right" ><img src="../abfahrtsmonitor/images/swwv-logo.png" width="100%" /></td>
                </tr>
            </table>
            <table>
                <tr valign="top">
                    <td valign="top">
                        <table>
                            <tr>
                                <th valign="top">Von</th>
                                <td valign="top"><input type="text" id="von" value="<?php echo date("d.m.Y H:i");?>"/></td>
                            </tr>
                            <tr>
                                <th valign="top">Bis</th>
                                <td valign="top"><input type="text" id="bis"></td>
                            </tr>
                            <tr>
                                <th valign="top">Text</th>
                                <td valign="top"><input type="text" id="text" size="90"></td>
                            </tr>
                            <tr height="95%">
                                <th valign="top">Haltestellen</th>
                                <td valign="top">
                                    <select id="haltestellenoptionen" onchange="select_haltestellen_optionen()">
                                        <option value="null"></option>
                                        <option value="all">Alle Haltestellen anzeigen</option>
                                        <option value="linie">Alle Haltestellen einer Linie anzeigen</option>
                                        <option value="stadt">Alle Haltestellen einer Stadt anzeigen</option>
                                    </select><img style="margin-left: 10px; visibility: hidden" src="../ajax-loader(4).gif" id="loader" />
                                    <div id="linien"></div>
                                    <div id="stadt"></div>
                                    <div id="haltestellen"></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td valign="top" style="min-width: 400px;" ">
                        <a onclick="speichern()">[ speichern ]</a>
                        <br><br><br>
                        <div id="haltestellenselect"></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <script type="text/javascript">
        $(function() {
            $( "#von" ).datetimepicker();
            $( "#bis" ).datetimepicker();
        });
    </script>
    </body>
    </html>
<?php
function get_stops($notification_id){
    $mysql = new MySQL();
    $query = "select gs.stop_id,gs.stop_name from gtfs_stops gs inner join ver_notification_gtfs_stops as vngs on (vngs.stop_id = gs.stop_id) where vngs.notification_id = ".$notification_id;
    $mysql->query($query);
    while($row = $mysql->fetchRow()){
        $stops[] = Array("id" => $row['stop_id'],"name" => str_replace("Wilhelmshaven ","",$row['stop_name']));
    }
    return $stops;
}
?>