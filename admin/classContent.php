<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 14.08.13
 * Time: 10:42
 * To change this template use File | Settings | File Templates.
 */

class Content{

    private $seite;
    private $values;

    public function __construct($seite,$values){
        $this->seite = $seite;
        $this->values = $values;

        $this->head();
        $this->navigation();
        $this->content();
        $this->foot();
    }

    private function head(){
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                    <title>SWWV Admin</title>
                    <script src="../js/jquery-1.9.1.min.js"></script>
                    <script src="../js/jquery.ui.core.js"></script>
                    <script src="../js/jquery.ui.widget.js"></script>
                    <script src="../js/jquery.ui.datepicker.js"></script>
                    <script src="../js/jquery-ui-timepicker-addon.js"></script>
                    <script src="../js/leaflet/leaflet.js"></script>
                    <script src="../js/leaflet/draw/leaflet.draw.js"></script>
                    <script src="../js/datetimepicker.settings.js"></script>
                    <link rel="shortcut icon" type="image/x-icon" href="../faviconswwv.ico" />
                    <link rel="stylesheet" href="../js/themes/base/jquery.ui.all.css">
                    <link rel="stylesheet" href="../js/leaflet/leaflet.css">
                    <link rel="stylesheet" href="../js/leaflet/draw/leaflet.draw.css">
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
                            $("#navigation-clock").html(stunde + ":" + minute + ":" + sekunde);
                        }
                        if($("#navigation-clock"))
                            setInterval("uhrzeit()",100);
                    </script>
                </head>
                <body style="margin: 0;">';

    }

    private function navigation(){
        ?>
            <div id="navigationwrapper">
                <div id="navigation">
                    <div id="navigation-title" onclick="location.href='?'"><img src="../swwv-logo.png"></div>
                    <div id="navigation-item" onclick="location.href='?seite=notifications'">Notifications</div>
                    <div id="navigation-item" onclick="location.href='?seite=linien'">Linien</div>
                    <div id="navigation-item" onclick="location.href='?seite=displays'">Displays</div>
                    <div id="navigation-item" onclick="location.href='?seite=extraabfahrten'">Extra Abfahrten</div>
                    <div id="navigation-item" onclick="location.href='?seite=import'">Import</div>
                    <div id="navigation-clock"><? date("H:i:s");?></div>
                </div>
            </div>
            <div id="wrapper">

        <?php
    }

    private function content(){
        echo '<div id="content">';
        switch($this->seite){
            case "notifications":
                $notification = new Notification();
                $notification->uebersicht();
                break;
            case "notificationdetails":
                $notification = new Notification($this->values['id']);
                $notification->details($this->values['action']);
                break;
            case "notificationneu":
                $notification = new Notification();
                $notification->neu();
                break;
            case "linien":
                $linien = new Linie();
                $linien->uebersicht();
                break;
            case "verlaufbearbeiten":
                $linien = new Linie($this->values['id']);
                $linien->verlauf_bearbeiten();
                break;
            case "displays":
                $display = new Display();
                $display->uebersicht();
                break;
            case "extraabfahrten":
                $specialdeparture = new SpecialDeparture();
                $specialdeparture->uebersicht();
                break;
            default:
                echo $this->seite;
        }
        echo "</div>";
    }

    private function foot(){
        echo '</div></body></html>';
    }
}