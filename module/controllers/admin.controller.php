<?php 

include_once ('base.controller.php'); 
include_once ('../model/admin.model.php');
class AdminController extends BASECONTROLLER{ 

    public $adminModel; 

    public function __construct($object){
        parent::__construct();

        $AModel=new AdminModel();  
        $AModel->parseObject($object);
        $v =[];
        $v = $AModel->ValidateAll();
        $v =array_merge($AModel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->adminModel = $AModel;
    }

    public function  checkExistUsername(){
        $result = [];
        $model=$this->adminModel;
        $stmt = $this->prepare("select * from adminschool where username=?");
        $stmt->bind_param("s", $model->username);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $data = [];
        while ($row = $rs->fetch_row()) {
            $result[]='user exist!';
        }
        $this->closeall($stmt);
        
        if(sizeof($result)>0){
            $m =' username %s , exist';
            $m = sprintf($m,$this->adminModel->username);
            PrintJSON([],$m,0);
            die();
        }

    }
    public function CreateUser(){  
        try{
            $this->checkExistUsername();
            parent::__construct();
            $model = $this->adminModel; 
            $model->password=$this->getPasswordHash($model->password);
            $sql="insert into adminschool(username,password,status,created_date,updated_date) values(?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sssss',$model->username,$model->password,$model->status,$model->createdate,$model->updatedate);
            $rs=$stmt->execute();
            if($rs){
                echo json_encode(array("message"=>"Create User Admin ID: $stmt->insert_id Success","status"=>"1"));
            }
            $this->closeall($stmt);

        }catch (Exception $e){
            print_r($e->getMessage());
        }
        
    }
    public function UpdateUser(){ 
        try{ 
            $this->CheckId(); 
            $createD=$this->getOldDate(); 
            $model=$this->adminModel;
            if(strlen($model->password)>55){
            $model->password=$this->getPasswordHash($model->password);
            }
            $sql="update adminschool set username=?, password=?, status=?, created_date=?, updated_date=? where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('ssssss', $model->username,$model->password,$model->status,$createD,$model->updatedate,$model->id);
            $data=$stmt->execute();
            if($data){
                PrintJSON([],"Update User Admin Id: $model->id Success Full",0);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function ViewUser(){
        try{ 
            $this->CheckId();
            $model=$this->adminModel;
            parent::__construct();
            $stmt = $this->prepare("select * from adminschool where id=?");  
            $stmt->bind_param('s', $model->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                        {
                            $arr= $v;
                        }
                        $data=json_encode($arr);
                        $json = "{\"Data\":$data, \"Message\": \"View User ID: $model->id Success Full\", \"Status\":\"1\"}";
                        echo $json; 
                        $this->closeall($stmt);
                        die;
                }else{
                    PrintJSON([],"User Admin Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function ViewAllUser(){
        try{  
            $model=$this->adminModel;
            parent::__construct();
            $stmt = $this->prepare("select * from adminschool");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                        {
                            $arr[]= $v;
                        }
                        $data=json_encode($arr);
                        $json = "{\"Data\":$data, \"Message\": \"View All User Success Full\", \"Status\":\"1\"}";
                        echo $json; 
                        $this->closeall($stmt);
                        die;
                }else{
                    PrintJSON([],"User Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function Delete(){
        try{
            $this->CheckId();
            $model=$this->adminModel;
            parent::__construct(); 
            $stmt = $this->prepare("delete from adminschool where id=?");  
            $stmt->bind_param('s', $model->id); 
            if($stmt->execute()){
                PrintJSON([],"Delete User Admin Id: $model->id Success Full",1);
                $this->closeall($stmt); 
            } 
            die;
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function getOldDate(){
        parent::__construct();
        $uModel =$this->adminModel; 
        $stmt = $this->prepare("select * from adminschool where id=?");  
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
        $model =$this->adminModel; 
        $stmt = $this->prepare("select * from adminschool where id=?");  
        $stmt->bind_param('s', $model->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "user Admin ID: $model->id is not available!", 0);
            die(); 
        }
        $this->closeall($stmt); 
    }
    
     
    public function DeleteUser($object){
        // $usermodel = new UserModel();
        // $usermodel->parseObject($object);
    }
}

?>