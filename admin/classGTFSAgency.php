<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 15.08.13
 * Time: 15:13
 * To change this template use File | Settings | File Templates.
 */

class GTFSAgency extends Obj{
    protected $name;
    protected $url;
    protected $timezone;
    protected $lang;
    protected $phone;
    protected $fare_url;

    protected function read(){
        $mysql = new MySQL();
        $mysql->query("select agency_name,agency_url,agency_timezone,agency_lang,agency_phone,agency_fare_url from gtfs_agency where agency_id = '".$this->id."'");
        $row = $mysql->fetchRow();
        $this->name = utf8_encode($row['agency_name']);
        $this->url = $row['agency_url'];
        $this->url = $row['agency_timezone'];
        $this->url = $row['agency_lang'];
        $this->url = $row['agency_phone'];
        $this->url = $row['agency_fare_url'];
    }
}