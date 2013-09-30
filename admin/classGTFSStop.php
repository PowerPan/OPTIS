<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 14.08.13
 * Time: 12:41
 * To change this template use File | Settings | File Templates.
 */

class GTFSStop extends Obj{
    private $code;
    private $desc;
    private $lat;
    private $lon;
    private $zone_id;
    private $url;
    private $location_type;
    private $parent_station;

    protected function read(){
        $mysql = new MySQL();
        $mysql->query("select stop_code,stop_name,stop_desc,stop_lat,stop_lon,zone_id,stop_url,location_type,parent_station from gtfs_stops where stop_id =".$this->id);
        $row = $mysql->fetchRow();
        $this->code = $row['stop_code'];
        $this->name = utf8_encode($row['stop_name']);
        $this->desc = $row['stop_desc'];
        $this->lat = $row['stop_lat'];
        $this->lon = $row['stop_lon'];
        $this->zone_id = $row['zone_id'];
        $this->url = $row['stop_url'];
        $this->location_type = $row['location_type'];
        $this->parent_station = $row['parent_station'];
    }
}