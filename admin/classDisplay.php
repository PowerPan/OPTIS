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
    private $start;
    private $notifications;
    private $standort;
    private $heartbeat;
    private $stopname;
    private $forceupdate;




    protected  function read(){
        $mysql = new MySQL();
        $mysql->query("select mac,textsize,scrollamount,start,notifications,standort,heartbeat,stopname,name,forceupdate from displays where id = ".$this->id);
        $row = $mysql->fetchRow();
        $this->mac = $row['mac'];
        $this->start = $row['start'];
        $this->name = utf8_encode($row['name']);
        $this->textsize = $row['textsize'];
        $this->notifications = $row['notifications'];
        $this->standort = utf8_encode($row['standort']);
        $this->heartbeat = $row['heartbeat'];
        $this->stopname = utf8_encode($row['stopname']);
        $this->scrollamount = $row['scrollamount'];

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

    public function uebersicht(){
        $mysql = new MySQL();
        $mysql->query("select id from displays");
        ?>
            <h2>Display Übersicht</h2>
            <table class="display-overview-table">
                <tr>
                    <th>MAC</th>
                    <th>Name</th>
                    <th>Standort</th>
                    <th>Haltestellenname</th>
                    <th>Letztes Lebenszeichen</th>
                    <th>Start</th>
                    <th>Textgröße</th>
                    <th>Notifications</th>
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
                                <td><?php echo $this->start ;?></td>
                                <td><?php echo $this->textsize ;?></td>
                                <td><?php echo $this->notifications ;?></td>
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