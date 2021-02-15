<?php 
include_once("../service/service.php");

class BASECONTROLLER extends MySqli{

    public string $host="localhost";
    public string $user="root";
    public string $pass="";
    public string $database;
    public function __construct() {  
        if(!empty($_SESSION['s'])){
            $dbname = $_SESSION['s'];
        }else{
            $dbname = 'adminschool_db';
        }
        parent::__construct($this->host, $this->user, $this->pass, $dbname);
    }
    public function setDB($db){
        // $this->database=$db;
        parent::__construct($this->host, $this->user, $this->pass, $db);  
    } 
    public function closeall($st){
        $st->close();
        $this->close();
    }

    public function getPasswordHash($pass){
        $p=password_hash($pass, PASSWORD_DEFAULT);
        return $p;
    }

}


?>