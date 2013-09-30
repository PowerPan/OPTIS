<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 14.08.13
 * Time: 11:56
 * To change this template use File | Settings | File Templates.
 */

class Obj {
    protected $id;
    protected $name;

    public function __construct($id = null){
        if($id){
            $this->set_id($id);
        }
    }

    public function set_id($id){
        $this->id = $id;
        $this->read();
    }

    public function get_id(){
        return $this->id;
    }

    public function get_name(){
        return $this->name;
    }

    public function set_name($name){
        $this->name = $name;
    }

    public function date_german2mysql($date){
        if(strlen($date) == 10){
            $date = explode(".",$date);
            return $date[2]."-".$date[1]."-".$date[0];
        }
        else{
            $date = explode(" ",$date);
            return $this->date_german2mysql($date[0])." ".$date[1];
        }

    }
}