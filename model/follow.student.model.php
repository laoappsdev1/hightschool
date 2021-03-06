<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class FollowStudentModel extends BASEMODEL{
    public int $id;  
    public int $levelstudentid;  
    public int $followteacherid;  
    public int $scorespecial;  
    public string $refer;  
    public int $replacement;  

    public function __construct()
    { 
        parent::__construct();
      
    }
    
    public function ValidateAll():array{ 
        $result =array();
        foreach($this as $property=>$value){
            $c =[];
            $c = $this->validate($property);
            sizeof($c)>0?$result[]=$c:'';  
        }
        return $result;
    }

    public function validate($propertys):array{
        switch($propertys){
            case "name":
            return $this->validateName(); 
        }
        return [];
    }
 

    function validateName():array{
        $result=array();
        if ($this->name === "") { 
            $result[]= '{"message":"Follow Student is empty","status":0}';
        }
        if (strlen($this->name) >255) { 
           $result[]= '{"message":"Follow Student is so long 255","status":0}';
        } 
        return $result;
    } 
    

    function validateId():array{
        $result =array(); 
        if(isset($this->id)){
            if($this->id==''|| !is_numeric($this->id)){
                $result [] ='ID is not a number or it\'s empty';
            }
        }
        return $result;
    }

}
?>