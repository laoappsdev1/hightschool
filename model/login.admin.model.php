<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class LogoutModel extends Tojson{
    public string $token;
}
class LoginResponseModel extends Tojson {
    public string $token;
    public string $loginTime;
    public string $loginIP;
    public function __construct()
    {

        $this->loginTime =( new DateTime())->format('Y-m-d H:i:s');;
        $this->loginIP = isset($_SESSION['loginip'])? $_SESSION['loginip']:'127.0.0.1';
        $this->token="dsfdsfad";
    }
} 

class LoginModel extends BASEMODEL {
    public string $username;
    public string $password;
    public string $token;

    public function __construct(){
        
    }

    function validateall():array{
        $result = [];
        foreach ($this as $property => $value) {
           $check= $this->validate($property);
           sizeof($check)>0?$result[]=$check:'';
        } 
        return $result;
    }

    function validate($p):array
    {
        switch ($p) {
            case 'username':
                return $this->validateUserName(); 
            case 'password':
                return $this->validatePassword(); 
        }
        return [];
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
    function validateUserName():array
    {
        $result=[];
        if ($this->username == "") { 
            $result[]= '{"message":"user name is empty","status":0}';
        }
        if (strlen($this->username) < 6) { 
           $result[]= '{"message":"user name is too short","status":0}';
        } 
        if (!preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $this->username)) { 
            $result[]= '{"message":"user name must be alphanumberic","status":0}';
        }
        return $result;
    }
      
}
?>