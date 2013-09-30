<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 16.09.13
 * Time: 09:14
 * To change this template use File | Settings | File Templates.
 */
//header('Content-Type: text/html; charset=iso-8859-1');
include_once("../../classMySQL.php");
if(isset($_GET['stop_id']))
    $stop_id = $_GET['stop_id'];
else
    $stop_id = "10420";
$mysql = new MySQL();
$mysql->query("select stop_name from gtfs_stops where stop_id = '".$stop_id."'");
$row = $mysql->fetchRow();
$rows = 20;
$start=0;
?>
<html>
    <head>
        <title>SWWV - WebMon</title>
        <link rel="stylesheet" href="jqtouch/themes/css/apple.css" title="jQTouch">

        <script src="../js/jquery-2.0.3.min.js" type="application/x-javascript" charset="utf-8"></script>
        <script src="jqtouch/jqtouch-jquery.js" type="application/x-javascript" charset="utf-8"></script>
        <script src="jqtouch/jqtouch.js" type="application/x-javascript" charset="utf-8"></script>





        <script type="text/javascript" charset="utf-8">
            var jQT = new $.jQTouch({
                //icon: 'jqtouch.png',
                //icon4: 'jqtouch4.png',
                addGlossToIcon: false,
                //startupScreen: 'jqt_startup.png',
                statusBar: 'black-translucent',
                themeSelectionSelector: '#jqt #themes ul'
                //preloadImages: []
            });

            // Some sample Javascript functions:
            $(function(){

                // Show a swipe event on swipe test
                /*
                $('#swipeme').swipe(function(evt, data) {
                    var details = !data ? '': '<strong>' + data.direction + '/' + data.deltaX +':' + data.deltaY + '</strong>!';
                    $(this).html('You swiped ' + details );
                    $(this).parent().after('<li>swiped!</li>')
                });

                $('#tapme').tap(function(){
                    $(this).parent().after('<li>tapped!</li>')
                });

                $('a[target="_blank"]').bind('click', function() {
                    if (confirm('This link opens in a new window.')) {
                        return true;
                    } else {
                        return false;
                    }
                });

                // Page animation callback events
                $('#pageevents').
                    bind('pageAnimationStart', function(e, info){
                        $(this).find('.info').append('Started animating ' + info.direction + '&hellip;  And the link ' +
                            'had this custom data: ' + $(this).data('referrer').data('custom') + '<br>');
                    }).
                    bind('pageAnimationEnd', function(e, info){
                        $(this).find('.info').append('Finished animating ' + info.direction + '.<br><br>');

                    });

                // Page animations end with AJAX callback event, example 1 (load remote HTML only first time)
                $('#callback').bind('pageAnimationEnd', function(e, info){
                    // Make sure the data hasn't already been loaded (we'll set 'loaded' to true a couple lines further down)
                    if (!$(this).data('loaded')) {
                        // Append a placeholder in case the remote HTML takes its sweet time making it back
                        // Then, overwrite the "Loading" placeholder text with the remote HTML
                        $(this).append($('<div>Loading</div>').load('ajax.html .info', function() {
                            // Set the 'loaded' var to true so we know not to reload
                            // the HTML next time the #callback div animation ends
                            $(this).parent().data('loaded', true);
                        }));
                    }
                });
                // Orientation callback event
                $('#jqt').bind('turn', function(e, data){
                    $('#orient').html('Orientation: ' + data.orientation);
                });
                */

            });


            function read_fahrplan(){
                $.post('../read_haltestellenplan.php?stop_id=<?php echo $stop_id; ?>&rows=<?php echo $rows; ?>&limitstart=<?php echo $start; ?>',function(data) {
                    var fahrplanObj = JSON.parse(data);
                    $('#anzeige #zeile').remove();
                    for(var i in fahrplanObj.fahrplan)
                        $("#anzeige").append('<li id="zeile"><div class="linie">'+ fahrplanObj.fahrplan[i].route_short_name+ '</div><div class="ziel">' + fahrplanObj.fahrplan[i].trip_headsign + '</div><div class="abfahrt">' + fahrplanObj.fahrplan[i].departure_time +'</div></li>');


                    /*if(!notification_row && fahrplanObj.notifications){
                        $("#fahrplan_zeile_"+rows).html("<td colspan='3' style='max-width: "+((screen.width)-60)+"px'><marquee scrollamount='<?php echo $scrollamount;?>'><div id='notification_marquee'>&nbsp;</div></marquee></td>");
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
                        notification_row = false;*/



                    setTimeout("read_fahrplan()",5000);

                })
                    .fail(function() { setTimeout("read_fahrplan()",10000); });
            }
            setTimeout("read_fahrplan()",100);

         </script>
        <style type="text/css" media="screen">
            #jqt.fullscreen #home .info {
                display: none;
            }
            div#jqt #about {
                padding: 100px 10px 40px;
                text-shadow: rgba(0, 0, 0, 0.3) 0px -1px 0;
                color: #999;
                font-size: 13px;
                text-align: center;
                background: #161618;
            }
            div#jqt #about p {
                margin-bottom: 8px;
            }
            div#jqt #about a {
                color: #fff;
                font-weight: bold;
                text-decoration: none;
            }

            .linie{
                display: inline;
                float: left;
                min-width: 50px;
                font-size: 18px;
            }

            .ziel{
                display: inline;
                float: left;
                min-width: 50px;
                font-size: 18px;
            }

            .abfahrt{
                display: inline;
                float: right;
                min-width: 50px;
                font-size: 18px;
            }
        </style>
    </head>
    <body>
        <div id="jqt">
            <div id="home" class="current">
                <div class="toolbar">
                    <h1><?php echo str_replace("Wilhelmshaven ","",$row['stop_name']); ?></h1>
                    <a class="button slideup" id="infoButton" href="#about">About</a>
                </div>
                <ul id="anzeige" class="metal scroll"></ul>
            </div>
        </div>
    </body>
</html>