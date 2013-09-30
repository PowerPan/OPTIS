<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 18.09.13
 * Time: 13:24
 * To change this template use File | Settings | File Templates.
 */

class SpecialDeparture extends Obj{
    protected $date;
    protected $route;
    protected $stop;
    protected $destination;
    protected $infotext;

    protected function read(){
        $mysql = new MySQL();
        $mysql->query("select DATE_FORMAT(date,'%d.%m.%Y %H:%i')date,stop_id,route,destination,infotext from special_departures where id = ".$this->id);
        $row = $mysql->fetchRow();
        $this->date = $row['date'];
        $this->route = utf8_encode($row['route']);
        $this->stop = new GTFSStop($row['stop_id']);
        $this->destination = utf8_encode($row['destination']);
        $this->infotext = utf8_encode($row['infotext']);
    }

    public function uebersicht(){
        echo "<h2>Übersicht</h2>";
        $mysql = new MySQL();
        $mysql->query("select id from special_departures where date > NOW() order by date");
        ?>
        <table border="5" width="100%">
            <tr>
                <th>Datum / Abfahrt</th>
                <th>Haltestelle</th>
                <th>Linie</th>
                <th>Ziel</th>
                <th>Infotext</th>
            </tr>

        <?php
        while($row = $mysql->fetchRow()){
            $this->set_id($row['id']);
            echo "<tr>";

            echo "<td>".$this->date."</td>";
            echo "<td>".$this->stop->get_name()."</td>";
            echo "<td>".$this->route."</td>";
            echo "<td>".$this->destination."</td>";
            echo "<td>".$this->infotext."</td>";

            echo "</tr>";
        }
        ?>
            </table>
        <?php
    }
}