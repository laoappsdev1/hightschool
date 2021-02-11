<?php
//namespace controllers;
include_once ('base.controller.php');
include_once ('../model/login.model.php');
class LoginController extends BASECONTROLLER{
    public $username="";
    public $password="";
    public $token="";
    public $usermodel;
    public function __construct($object)
    {
        parent::__construct();  
        $uModel=new LoginModel();  
        $uModel->parseObject($object);
        $c=$uModel->validateall();
        if(sizeof($c)>0){
            echo json_encode($c);
            die;
        } 
        $this->usermodel=$uModel;   
    }

    public function login(){
        $this->checkPassword(); 
        $user=[];
        $res = new LoginResponseModel();
        $res->token =registerToken($this->getUsertype());
        PrintJSON([$res],'login ok',1);     
        die;   
    } 

    function checkPassword(){
        $model=$this->usermodel;
        if(!password_verify($model->password,$this->getPassword())){
            PrintJSON([],"Password Can't Valiable!",0);
            die;
        }

    }
    
    function getPassword(){
        $model=$this->usermodel;
        parent::__construct();
        $stmt=$this->prepare("select password from user where username=?");
        $stmt->bind_param('s',$model->username);
        $stmt->execute();
        $result=$stmt->get_result();
        if(!empty($result->num_rows)){
            $pass='';
            foreach($result as $k=>$v){
                $pass=$v['password'];
            }  
            return $pass; 
            die();
        }else{
           PrintJSON([],"User Name Can't Valiable!",0);
           die;
        }
    }

    function getUsertype(){
        $model=$this->usermodel;
        parent::__construct();
        $stmt=$this->prepare("select usertype, password, username from user where username=?");
        $stmt->bind_param('s',$model->username);
        $stmt->execute();
        $result=$stmt->get_result();
        if(!empty($result->num_rows)){
            $usertype='';
            foreach($result as $k=>$v){
                $usertype=$v['usertype'];
            }  
            return $usertype; 
            die();
        }else{
           PrintJSON([],"User Name Can't Valiable!",0);
           die;
        }
    }
}

?>