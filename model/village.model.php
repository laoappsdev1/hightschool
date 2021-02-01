<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class VillageModel extends BASEMODEL{
    public int $id;  
    public int $provinceid;  
    public string $districtid;  
    public string $name;  

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
            case "provinceid":
            return $this->validateProvinceID(); 
            case "districtid":
            return $this->validateDistrictID(); 
        }
        return [];
    }
 

    function validateName():array{
        $result=array();
        if ($this->name === "") { 
            $result[]= '{"message":"Village name is empty","status":0}';
        }
        if (strlen($this->name) >255) { 
           $result[]= '{"message":"Village name is so long 255","status":0}';
        } 
        return $result;
    } 
    function validateProvinceID():array{
        $result=array();
        if ($this->provinceid === "") { 
            $result[]= '{"message":"Province Id is empty","status":0}';
        }
        if (strlen($this->provinceid) >255) { 
           $result[]= '{"message":"Province Id is so long 255","status":0}';
        } 
        return $result;
    } 
    
    function validateDistrictID():array{
        $result=array();
        if ($this->districtid === "") { 
            $result[]= '{"message":"Disctrict Id is empty","status":0}';
        }
        if (strlen($this->districtid) >255) { 
           $result[]= '{"message":"District Id is so long 255","status":0}';
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