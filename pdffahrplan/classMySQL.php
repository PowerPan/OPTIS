<?php

/**
 * @author ohyeah
 * @copyright 2012
 */

class MySQL {
  private $connection = NULL;
  private $result = NULL;
  private $counter=NULL;
  private $host = "localhost";
  private $database = "swwv_bus";
  private $user = "root";
  private $pass = "";
  public $last_insert_id;
 
 
  public function __construct(){
	$this->connection = mysql_pconnect($this->host,$this->user,$this->pass,TRUE);
  	mysql_select_db($this->database, $this->connection);
  }
 
  public function disconnect() {
    if (is_resource($this->connection))				
        mysql_close($this->connection);
  }
 
  public function query($query) {
  	$this->result=mysql_query($query,$this->connection);
    if(mysql_error()){
        echo mysql_error();
        
        echo $query;
        }
    
    $this->last_insert_id = mysql_insert_id();
  	$this->counter=NULL;
  }
 
  public function fetchRow() {
  	return mysql_fetch_assoc($this->result);
  }
  
  public function count() {
  	if($this->counter==NULL && is_resource($this->result)) {
  		$this->counter=mysql_num_rows($this->result);
  	} 
	return $this->counter;
  }
}

?>