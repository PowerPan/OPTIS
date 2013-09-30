<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 15.08.13
 * Time: 14:26
 * To change this template use File | Settings | File Templates.
 */

class GTFSRoutes extends Obj{
    protected $agency;
    protected $short_name;
    protected $long_name;
    protected $desc;
    protected $type;
    protected $url;
    protected $color;
    protected $text_color;

    protected function read(){
        $mysql = new MySQL();
        $mysql->query("select agency_id,route_short_name,route_long_name,route_desc,route_type,route_url,route_color,route_text_color from gtfs_routes where route_id =".$this->id);
        $row = $mysql->fetchRow();
        $this->agency = new GTFSAgency($row['agency_id']);
        $this->short_name = utf8_encode($row['route_short_name']);
        $this->long_name = utf8_encode($row['route_long_name']);
        $this->desc = utf8_encode($row['route_desc']);
        $this->type = $row['route_type'];
        $this->url = $row['route_url'];
        $this->color = $row['route_color'];
        $this->text_color = $row['route_text_color'];
    }
}