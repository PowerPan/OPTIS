<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 16.08.13
 * Time: 10:18
 * To change this template use File | Settings | File Templates.
 */

class Display extends Obj{
    private $mac;
    private $stops;
    private $textsize;
    private $scrollamount;
    private $rows;
    private $start;
    private $notifications;
    private $standort;
    private $heartbeat;
    private $stopname;
    private $forceupdate;
    private $last_settings;
    private $last_notification;
    private $last_departure;

    protected  function read(){
        $mysql = new MySQL();
        $mysql->query("select mac,textsize,scrollamount,start,rows,notifications,standort,heartbeat,last_settings,last_notification,last_departure,stopname,name,forceupdate from displays where id = ".$this->id);
        $row = $mysql->fetchRow();
        $this->mac = $row['mac'];
        $this->start = $row['start'];
        $this->rows = $row['rows'];
        $this->name = utf8_encode($row['name']);
        $this->textsize = $row['textsize'];
        $this->notifications = $row['notifications'];
        $this->standort = utf8_encode($row['standort']);
        $this->heartbeat = $row['heartbeat'];
        $this->stopname = utf8_encode($row['stopname']);
        $this->scrollamount = $row['scrollamount'];
        $this->last_settings = $row['last_settings'];
        $this->last_notification = $row['last_notification'];
        $this->last_departure = $row['last_departure'];

        if($row['forceupdate'] == 0)
            $this->forceupdate = 0;
        else
            $this->forceupdate = 1;

        $mysql->query("select stop_id from ver_displays_stops where display_id = ".$this->id);
        while($row = $mysql->fetchRow()){
            $this->stops[] = new GTFSStop($row['stop_id']);
        }

    }

    public function read_with_mac($mac){
        $mysql = new MySQL();
        $mysql->query("select id from displays where mac = '".$mac."'");
        $row = $mysql->fetchRow();
        $this->set_id($row['id']);
    }

    public function heartbeat(){
        $mysql = new MySQL();
        $mysql->query("update displays set heartbeat = NOW() where id = ".$this->id);
    }

    public function get_textsize(){
        return $this->textsize;
    }

    public function get_start(){
        return $this->start;
    }

    public function get_scrollamount(){
        return $this->scrollamount;
    }

    public function get_rows(){
        return $this->scrollamount;
    }

    public function set_rows($rows){
        $this->rows = $rows;
        $mysql = new MySQL();
        $mysql->query("update displays set rows = '".$this->rows."' where id = ".$this->id);
    }

    public function get_stopname(){
        return $this->stopname;
    }

    public function get_stops(){
        return $this->stops;
    }

    public function get_stops_list(){
        foreach ($this->stops as $stop){
            $stoplist[] = $stop->get_id();
        }
        return implode(",",$stoplist);
    }

    public function get_notifications(){
        return $this->notifications;
    }

    public function get_forceupdate(){
        return $this->forceupdate;
    }

    public function set_forceupdate_off(){
        $this->forceupdate = 0;
        $this->set_forceupdate();
    }

    public function set_forceupdate_on(){
        $this->forceupdate = 1;
        $this->set_forceupdate();
    }

    private function set_forceupdate(){
        $mysql = new MySQL();
        $mysql->query("update displays set forceupdate = ".$this->forceupdate." where id = ".$this->id);
    }

    public function bearbeiten(){
        ?>
        <h2>Display Details bearbeiten</h2>
        <table>
            <tr>
                <th>Name</th>
                <td><?php echo $this->name;?></td>
            </tr>
            <tr>
                <th>Standort</th>
                <td><?php echo $this->standort;?></td>
            </tr>
            <tr>
                <th>MAC</th>
                <td><?php echo $this->mac;?></td>
            </tr>
            <tr>
                <th>Haltestellenname</th>
                <td><?php echo $this->stopname;?></td>
            </tr>
            <tr>
                <th>Schriftgröße</th>
                <td><?php echo $this->textsize;?></td>
            </tr>
            <tr>
                <th>Notificationgeschwindigkeit</th>
                <td><div id="scrollamountslider"></div>
                    <?php echo $this->scrollamount;?></td>
            </tr>

            <tr>
                <th>Letztes Kontakt</th>
                <td><?php echo $this->heartbeat;?></td>
            </tr>
            <tr>
                <th>Letztes Abruf Einstellungen</th>
                <td><?php echo $this->last_settings;?></td>
            </tr>
            <tr>
                <th>Letztes Abruf Notificaions</th>
                <td><?php echo $this->last_notification;?></td>
            </tr>
            <tr>
                <th>Letztes Abruf Abfahrten</th>
                <td><?php echo $this->last_departure;?></td>
            </tr>
        </table>
        <script>
            $(function() {
                $( "#scrollamountslider" ).slider();
            });
        </script>
        <?php
    }

    public function details(){
        ?>
            <h2>Display Details</h2>
            <table>
                <tr>
                    <th>Name</th>
                    <td><?php echo $this->name;?></td>
                </tr>
                <tr>
                    <th>Standort</th>
                    <td><?php echo $this->standort;?></td>
                </tr>
                <tr>
                    <th>MAC</th>
                    <td><?php echo $this->mac;?></td>
                </tr>
                <tr>
                    <th>Haltestellenname</th>
                    <td><?php echo $this->stopname;?></td>
                </tr>
                <tr>
                    <th>Schriftgröße</th>
                    <td><?php echo $this->textsize;?></td>
                </tr>
                <tr>
                    <th>Notificationgeschwindigkeit</th>
                    <td><?php echo $this->scrollamount;?></td>
                </tr>

                <tr>
                    <th>Letztes Kontakt</th>
                    <td><?php echo $this->heartbeat;?></td>
                </tr>
                <tr>
                    <th>Letztes Abruf Einstellungen</th>
                    <td><?php echo $this->last_settings;?></td>
                </tr>
                <tr>
                    <th>Letztes Abruf Notificaions</th>
                    <td><?php echo $this->last_notification;?></td>
                </tr>
                <tr>
                    <th>Letztes Abruf Abfahrten</th>
                    <td><?php echo $this->last_departure;?></td>
                </tr>
            </table>
            <br>
            <a onclick="set_display_force_update(<?php echo $this->id;?>)">[ Force Update ]</a>
            <a href="?seite=displayedit&id=<?php echo $this->id;?>">[ Einstellungen Bearbeiten ]</a>
            <a >[ Zurück ]</a>
            <script type="text/javascript">
                function set_display_force_update(display_id){
                    $.get('ajax.php?func=set_display_force_update&id='+ display_id,function(data){
                        alert("Force Update gesetzt, bei der nächsten Konaktaufnahme werden alle Daten neu an das Display übertragen");
                    })
                }
            </script>
        <?php
    }

    public function set_force_update($display_id){
        $mysql = new MySQL();
        $mysql->query("update displays set forceupdate = 1 where id =".$display_id);
    }

    public function uebersicht(){
        $mysql = new MySQL();
        $mysql->query("select id from displays");
        ?>
            <h2>Display Übersicht</h2>
            <a href="?seite=neuesdisplay">[ Neues Display ]</a>
            <br>
            <br>
            <table class="display-overview-table">
                <tr>
                    <th>MAC</th>
                    <th>Name</th>
                    <th>Standort</th>
                    <th>Haltestellenname</th>
                    <th>Letztes Lebenszeichen</th>

                </tr>
                <?php
                    while($row = $mysql->fetchRow()){
                        $this->set_id($row['id']);
                        ?>
                            <tr>
                                <td><?php echo $this->mac ;?></td>
                                <td><?php echo $this->name ;?></td>
                                <td><?php echo $this->standort ;?></td>
                                <td><?php echo $this->stopname ;?></td>
                                <td><?php echo $this->heartbeat ;?></td>
                                <td><a href="?seite=display&id=<?php echo $this->id ;?>">[ Details ]</a></td>
                            </tr>
                        <?php
                    }
                ?>
            </table>
        <?php
    }

    private function build_url(){
        $prefix = "http://192.168.250.16/swwv/abfahrtsmonitor/?";
        $urlelemente[] = "stop_id=".$this->stop->get_id();
        $urlelemente[] = "rows=".$this->zeilen;
        $urlelemente[] = "start=".$this->start;
        $urlelemente[] = "headersize=".$this->headersize;
        $urlelemente[] = "textsize=".$this->textsize;
        $urlelemente[] = "notificationsize=".$this->notificationsize;
        if($this->header == 0)
            $urlelemente[] = "headeroff";
        if($this->notifications == 0)
            $urlelemente[] = "notificationsoff";
        if($this->firstrow == 0)
            $urlelemente[] = "firstrowoff";

        $this->url = $prefix.implode("&",$urlelemente);

        $mysql = new MySQL();
        $mysql->query("update displays set url = '".$this->url."' where id = ".$this->id);

    }
}