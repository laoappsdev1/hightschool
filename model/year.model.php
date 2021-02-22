<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class YearsModel extends BASEMODEL{
    public int $id;
    public int $year;
    public string $schoolyear; 
    public string $series; 


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
            case "year":
            return $this->year();
            case "series":
            return $this->series();
            case "schoolyear":
            return $this->schoolyear();
        }
        return [];
    }

    function schoolyear():array{
        $result=array();
        if ($this->schoolyear === "") { 
            $result[]= '{"message":"schoolyear is empty","status":0}';
        }
        return $result;
    }

    function year():array{
        $result=array();
        if ($this->year === "") { 
            $result[]= '{"message":"year is empty","status":0}';
        }
        return $result;
    }
    
    function series():array{
        $result=array();
        if ($this->series === "") { 
            $result[]= '{"message":"series is empty","status":0}';
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
