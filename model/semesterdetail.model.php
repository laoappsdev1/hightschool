<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');

class SemesterDetailModel extends BASEMODEL{
    public int $id;  
    public int $semesterid;  
    public string $month;  

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
            case "month":
            return $this->validateMonth(); 
        }
        return [];
    }
 

    function validateMonth():array{
        $result=array();
        if ($this->month === "") { 
            $result[]= '{"message":"Semester Detail Month is empty","status":0}';
        }
        if (!in_array($this->month, semesterdetailmonth)) {  
            $result[]= '{"message":"Semester Detail Month: '.$this->month.' is not Valiable","status":0}';
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