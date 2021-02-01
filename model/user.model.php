<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class UserModel extends BASEMODEL{
    public int $id;
    public string $username;
    public string $password;
    public string $token;
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
            case "username":
            return $this->validateUsername();
            case "password":
            return $this->validatePassword();
            case "status":
            return $this->validateStatus();
        }
        return [];
    }

    function validateUserName():array{
        $result=array();
        if ($this->username === "") { 
            $result[]= '{"message":"user name is empty","status":0}';
        }
        
        if (strlen($this->username) < 3) { 
           $result[]= '{"message":"user name is too short","status":0}';
        } 
        if (!preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $this->username)) { 
            $result[]= '{"message":"user name must be alphanumberic","status":0}';
        }

        return $result;
    }
    function validatePassword():array
    {
        $uppercase = preg_match('@[A-Z]@', $this->password);
        $lowercase = preg_match('@[a-z]@', $this->password);
        $number = preg_match('@[0-9]@', $this->password);
        $result = [];
        if (!$uppercase || !$lowercase || !$number || strlen($this->password) < 6) { 
           $result[]= '{"message":"password must have uppercase lowercase and number with at least 6 digits","status":0}';
        } 
        return $result;
    }
    
    public function validateStatus():array{
        $result =array();
        if(!in_array($this->status, usertype)){
            PrintJSON(""," user status: ".$this->status. " is not available!", 0);
            die();
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
