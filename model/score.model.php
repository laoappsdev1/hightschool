<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class ScoreModel extends BASEMODEL{
    public int $id;  
    public int $score;  
    public int $followstudentid;  
    public int $semesterdetailid;  
    public string $description;  

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
            case "score":
            return $this->validateScore(); 
        }
        return [];
    }
 

    function validateScore():array{
        $result=array();
        if ($this->score === "") { 
            $result[]= '{"message":"score is empty","status":0}';
        }
        if($this->score==''|| !is_numeric($this->score)){
           $result[]= '{"message":"Your Score is not a number or it is empty","status":0}';
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