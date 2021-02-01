<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class ClassRoomModel extends BASEMODEL{
    public int $id;  
    public int $levelid;  
    public string $classnumber;  

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
            case "classnumber":
            return $this->validateName(); 
        }
        return [];
    }
 

    function validateName():array{
        $result=array();
        if ($this->classnumber === "") { 
            $result[]= '{"message":"Class Number is empty","status":0}';
        }
        if (strlen($this->classnumber) >12) { 
           $result[]= '{"message":"Class Number is so long 12!","status":0}';
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