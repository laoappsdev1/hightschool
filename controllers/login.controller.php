<?php
//namespace controllers;
include_once ('base.controller.php');
include_once ('../model/login.model.php');
class LoginController extends BASECONTROLLER{
    public $username="";
    public $password="";
    public $token="";
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
        $sql="select id, username from user where username=? and password=?"; 
        $stmt = $this->prepare($sql);
        $stmt->bind_param("ss", $uModel->username, $uModel->password);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
         if(!empty($rs->num_rows)){
            $result=[];
            foreach($rs as $key=>$val){
               $result=$val['username'];
            }  
            echo json_encode(array('status' => "1",
                             'token' => registerToken($result),
                             'data'=> "Register Token Success Fully",
                            ));
              
            //  echo registerToken($result);
            exit;             
            $this->closeall($stmt);
         }else{
            PrintJSON([],'Username or Password Not Avaliable',0);
            die;
         }
       
    }
    public function login(){
        // $this->query("select*from user"); 
        // $user=[];
        // $res = new LoginResponseModel();
        // $res->token =registerToken($user);
        // PrintJSON([$res],'login ok',1);

        
    }   
}

?>