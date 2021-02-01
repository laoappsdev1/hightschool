<?php 

include_once ('base.controller.php');
include_once ('../model/user.model.php');
class UserController extends BASECONTROLLER{ 

    public $userModel; 

    public function __construct($object){
        parent::__construct();

        $uModel=new UserModel();  
        $uModel->parseObject($object);
        $v =[];
        $v = $uModel->ValidateAll();

        $v =array_merge($uModel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->userModel = $uModel;
    }

    public function  checkExistUsername(){
        $result = [];
        $uModel=$this->userModel;
        $stmt = $this->prepare("select * from user where username=?");
        $stmt->bind_param("s", $uModel->username);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $data = [];
        while ($row = $rs->fetch_row()) {
            $result[]='user exist!';
        }
        $this->closeall($stmt);
        
        if(sizeof($result)>0){
            $m =' username %s , exist';
            $m = sprintf($m,$this->userModel->username);
            PrintJSON([],$m,0);
            die();
        }

    }
    public function CreateUser(){  
        try{
            $this->checkExistUsername();
            parent::__construct();

            $uModel = $this->userModel;
            $sql="insert into user(username,password,token,status,created_date,updated_date) values(?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssss',$uModel->username,$uModel->password,$uModel->token,$uModel->status,$uModel->createdate,$uModel->updatedate);
            $rs=$stmt->execute();
            if($rs){
                // $id = isset($uModel->id)?$uModel->id: '';
                echo json_encode(array("user id","message"=>"Create User Success","status"=>"1"));
            }
            
            // $a = array('user'=>$uModel);
            // $token =registerToken($a);
            // echo json_encode($token);

            $this->closeall($stmt);

        }catch (Exception $e){
            print_r($e->getMessage());
        }
        
    }
    public function UpdateUser(){ 
        try{ 
            $this->CheckId(); 
            $createD=$this->getOldDate(); 
            $uModel=$this->userModel;
            $sql="update user set username=?, password=?, token=?, status=?, created_date=?, updated_date=? where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('sssssss', $uModel->username,$uModel->password,$uModel->token,$uModel->status,$createD,$uModel->updatedate,$uModel->id);
            $data=$stmt->execute();
            if($data){
                PrintJSON([],"Update User Id: $uModel->id Success Full",0);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function Views(){
        try{ 
            $this->CheckId();
            $uModel=$this->userModel;
            parent::__construct();
            $stmt = $this->prepare("select * from user where id=?");  
            $stmt->bind_param('s', $uModel->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $emparray = array();
            if(!empty($rs->num_rows)){
                foreach($rs as $k=>$v)
                {
                    $emparray[] = $v;
                }
                    $jsonobj='{"Data":'.json_encode($emparray).',"Message":"Select Data Success Full","status":1}';
                    echo json_encode($jsonobj);
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"User Id: $uModel->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function Delete(){
        try{
            $this->CheckId();
            $uModel=$this->userModel;
            parent::__construct(); 
            $stmt = $this->prepare("delete from user where id=?");  
            $stmt->bind_param('s', $uModel->id); 
            if($stmt->execute()){
                PrintJSON([],"Delete User Id: $uModel->id Success Full",1);
                $this->closeall($stmt); 
            } 
            die;
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function getOldDate(){
        parent::__construct();
        $uModel =$this->userModel; 
        $stmt = $this->prepare("select * from user where id=?");  
        $stmt->bind_param('s', $uModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $createD=[];
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;  
    }

    public function CheckId(){  
        $uModel =$this->userModel; 
        $stmt = $this->prepare("select * from user where id=?");  
        $stmt->bind_param('s', $uModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "user ID: $uModel->id is not available!", 0);
            die(); 
        }
        $this->closeall($stmt); 
    }
    
    public function ViewUser($object){
        // $usermodel = new UserModel();
        // $usermodel->parseObject($object);
    }
    public function DeleteUser($object){
        // $usermodel = new UserModel();
        // $usermodel->parseObject($object);
    }
}

?>