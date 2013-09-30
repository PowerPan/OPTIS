<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 14.08.13
 * Time: 10:42
 * To change this template use File | Settings | File Templates.
 */

class Linie extends GTFSRoutes{
    protected $farbe;
    protected $verlauf;
    protected $icon;
    protected $name;

    protected function read(){
        parent::read();
        $mysql = new MySQL();
        $mysql->query("select farbe,name,icon,icon_lat,icon_lng,icon_x,icon_y from linien where route_id = ".$this->id);
        $row = $mysql->fetchRow();
        $this->farbe = $row['farbe'];
        $this->name = utf8_encode($row['name']);
        $this->icon = Array("src" => $row['icon'],"lat" => $row['icon_lat'],"lng" => $row['icon_lng'],"x" => $row['icon_x'],"y" => $row['icon_y']);

        $mysql->query("select lat,lng,sort from linien_verlauf where route_id = ".$this->id." order by sort");
        while($row = $mysql->fetchRow())
            $this->verlauf[] = $row;
    }

    public function uebersicht(){
        $mysql = new MySQL();
        $mysql->query("select route_id from linien order by name");
        ?>
        <h2>Linien</h2>
        <table>
            <tr>
                <th>Linie</th>
                <th></th>
                <th>Farbe</th>
            </tr>
            <?php
                while($row = $mysql->fetchRow()){
                    $linie = new Linie($row['route_id']);
                    ?>
                        <tr>
                            <td><?php echo $linie->name; ?></td>
                            <td><img src="../images/<?php echo $linie->icon['src']; ?>" /></td>
                            <td><?php echo $linie->farbe; ?></td>
                            <td>
                                <?php
                                    if(is_array($linie->verlauf)){
                                        echo "<a href='?seite=verlaufbearbeiten&id=".$linie->id."'>[ Verlauf bearbeiten ]</a> ";
                                    }
                                ?>
                            </td>

                        </tr>
                    <?php
                }
            ?>
        </table>
        <?php
    }

    public function get_name(){
        return $this->name;
    }

    public function verlauf_bearbeiten(){
        $map = new Map();
        $map->add_draw_edit_polyline();
        $map->add_marker($this->icon['lat'],$this->icon['lng'],$this->id,"http://localhost/swwv/images/".$this->icon['src'],$this->icon['x'],$this->icon['y']);
        $map->add_polyline(json_encode($this->verlauf),$this->farbe,$this->id);
    }

    public function save_new_icon_posiion($lat,$lng){
        $this->icon['lat'] = $lat;
        $this->icon['lng'] = $lng;
        $mysql = new MySQL();
        $mysql->query("update linien set icon_lat = ".$lat.",icon_lng = ".$lng." where route_id = ".$this->id);
    }

    public function save_new_verlauf($latlngs){
        $mysql = new MySQL();
        $mysql->query("delete from linien_verlauf where route_id = ".$this->id);
        $sort = 0;
        print_r($latlngs);
        foreach($latlngs as $latlng){
            $mysql->query("insert into linien_verlauf (route_id,lat,lng,sort) values (".$this->id.",".$latlng[1].",".$latlng[0].",".$sort.")");
            $sort++;
        }
    }
}

?>