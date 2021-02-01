<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class StudentModel extends BASEMODEL{
    public int $id; 
    public int $parentid;
    public string $firstname; 
    public string $lastname;
    public string $dob;
    public string $gender;
    public string $tel;       //null
    public string $fromschool;   //null
    public string $img;       //null
    public int $villageid; 



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
            case "firstname":
            return $this->validateFirstname();
            case "lastname":
            return $this->validateLastname();
            case "dob":
            return $this->validateDob(); 
            case "village_id":
            return $this->validateVillage();
            case "gender":
            return $this->validateGender();
            case "parentid":
            return $this->validateParentid();
        }
        return [];
    }
 
    function validateFirstname():array{
        $result=array();
        if ($this->firstname === "") { 
            $result[]= '{"message":"your first name is empty","status":0}';
        }
        if (strlen($this->firstname) >255) { 
           $result[]= '{"message":"your first name is so long 255","status":0}';
        } 
        return $result;
    }

    function validateLastname():array{
        $result=array();
        if ($this->lastname === "") { 
            $result[]= '{"message":"your last name is empty","status":0}';
        }
        if (strlen($this->lastname) >255) { 
           $result[]= '{"message":"your last name is so long 255","status":0}';
        } 
        return $result;
    }

    function validateGender(){
        $result =array();
        if(!in_array($this->gender, gender)){
            PrintJSON(""," your Gender: ".$this->gender. " is not available!", 0);
            die();
        }    
        return $result;
    }

    function validateVillage(){
        $result=array();
                
        if (!is_numeric($this->villageid) || $this->villageid === "") { 
           $result[]= '{"message":"Village ID Empty Or It is not int","status":0}';
        } 

        return $result;
    }

    function validateDob():array{
        $result=array();
        if ($this->dob === "") { 
            $result[]= '{"message":"your dob is empty","status":0}';
        }
        
        if (!$this->validateDate($this->dob)) { 
           $result[]= '{"message":"your dob is not format date Y-m-d","status":0}';
        } 

        return $result;
    }

    function validateTel():array{
        $result=array();
        if ($this->tel === "") { 
            $result[]= '{"message":"your number phone is empty","status":0}';
        }
        
        if (strlen($this->tel) > 24) { 
           $result[]= '{"message":"Your number phone is so long more 24 charecter","status":0}';
        } 

        return $result;
    }

    function validateParentid():array{
        $result=array();
        if($this->parentid==''|| !is_numeric($this->parentid)){
            $result [] ='Parent id is not a Int or it\'s empty';
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
