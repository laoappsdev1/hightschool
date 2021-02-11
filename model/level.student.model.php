<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class LevelStudentModel extends BASEMODEL{
    public int $id;  
    public int $studentid;  
    public int $levelid;  
    public int $classroomid;  
    public string $description;  
    public string $status;  

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
            case "status":
            return $this->validateStatus(); 
        }
        return [];
    }
 

    function validateStatus():array{
        $result=array();
        if ($this->status === "") { 
            $result[]= '{"message":"Your Level Student Status is empty","status":0}';
        }
        if (!in_array($this->status, checkStudentLevelStatus)) { 
           $result[]= '{"message":"Your Level Student Status not Valiable!","status":0}';
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