<?php 
include_once("../service/service.php");
class BASECONTROLLER extends MySqli{
    public string $host="localhost";
    public string $user="root";
    public string $pass="";
    public string $database="hightschool_db";
    public function __construct() {
        parent::__construct($this->host, $this->user, $this->pass, $this->database);
    }
    public function closeall($st){
        $st->close();
        $this->close();
    }

}


?>