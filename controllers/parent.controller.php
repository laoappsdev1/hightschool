<?php 

include_once ('base.controller.php');
include_once ('../model/parent.model.php');
include_once ('../model/user.model.php');
class ParentController extends BASECONTROLLER{ 

    public $userModel; 
    public $PModel; 
    public $userId; 

    public function __construct($object){
        parent::__construct();
        
        // user
        $uModel=new UserModel();  
        $uModel->parseObject($object);
        $v =[];
        $v = $uModel->ValidateAll();
        $v =array_merge($uModel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
            // parent
        $ParentModel=new ParentModel();  
        $ParentModel->parseObject($object);
        $v2 =[];
        $v2 = $ParentModel->ValidateAll();

        $v2 =array_merge($ParentModel->validateId(),$v2);
        if(sizeof($v2)>0){
            echo json_encode($v2);
            die();
        }

        $this->userModel = $uModel;
        $this->PModel = $ParentModel;  
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
    public function createParent(){  
        try{  
            $this->checkExistUsername();
            $this->CheckVillageId();

            // create user
            parent::__construct();
            $model = $this->userModel;
            $model->password=$this->getPasswordHash($model->password); 
            $sql="insert into user(username,password,token,usertype,created_date,updated_date) values(?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssss',$model->username,$model->password,$model->token,$model->usertype,$model->createdate,$model->updatedate);
            $stmt->execute();
            $this->userId=$stmt->insert_id;  
            $this->closeall($stmt);

            //create parent
            parent::__construct();  

            $prModel = $this->PModel;  
            $sql2="insert into parent(description,firstname,lastname,gender,village_id,job,tel,email,user_id) values(?,?,?,?,?,?,?,?,?)";
            $stmt2 = $this->prepare($sql2);
            $stmt2->bind_param('sssssssss',$prModel->description,$prModel->firstname,$prModel->lastname,$prModel->gender,$prModel->villageid,$prModel->job,$prModel->tel,$prModel->email,$this->userId);
            if($stmt2->execute()){ 
                PrintJSON([],"Create Parent ID: $stmt2->insert_id Success Full","1");
                $this->closeall($stmt2);
            }else{
                print_r($stmt2->error);
            } 
        }catch (Exception $e){
            print_r($e->getMessage());
        }
    }

    function getOldpassword(){
        parent::__construct();
        $model=$this->userModel;
        $sql="select password from user";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $result=$stmt->get_result();
    foreach($result as $k=>$v){
        $pass=$v['password'];
    }
        return $pass;
    }
    function getPasswordUpdateUser(){
        $model=$this->userModel;
        if(strlen($model->password)>55){  
            $model->password=$this->getOldpassword();  
        }else{
            $model->password=$this->getPasswordHash($model->password); 
        } 
    }

    public function updateParent(){ 
        try{ 
            $this->CheckParentId();
            $this->CheckVillageId();    
            $createD=$this->getDateCreate(); 

            // //update user 
            parent::__construct();
            $model=$this->userModel;
            $this->getPasswordUpdateUser();
             
            $sql="update user set username=?, password=?, token=?, usertype=?, created_date=?, updated_date=? where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('sssssss', $model->username,$model->password,$model->token,$model->usertype,$createD,$model->updatedate,$this->PModel->userid);
            $stmt->execute();
            $this->closeall($stmt);
            
            //update Parent
            parent::__construct();
 
            $prModel=$this->PModel;
            $sql2="update parent set firstname=?, lastname=?, gender=?,village_id=?, description=?, tel=?, email=?, user_id=? , job=?  where id=?";
            $stmt2=$this->prepare($sql2);
            $stmt2->bind_param('ssssssssss', $prModel->firstname,$prModel->lastname,$prModel->gender,$prModel->villageid,$prModel->description,$prModel->tel,$prModel->email,$prModel->userid,$prModel->job,$prModel->id);
            
            if($stmt2->execute()){
                PrintJSON([],"Update Parent Id: $prModel->id Success Full",1);
            }

        }catch(Exception $e){
            print_r($e->getMessage());
        }
    } 

    public function deleteParent(){
        try{   
            $this->CheckParentId(); 
            $this->userId=$this->getUserId();
            // $this->DeleteOldFile(); 

            // delete Teacher
            $prModel=$this->PModel;
            parent::__construct(); 
            $stmt = $this->prepare("delete from parent where id=?");  
            $stmt->bind_param('s', $prModel->id); 
            $stmt->execute(); 
            $this->closeall($stmt); 

            // delete user
            $uModel=$this->userModel;
            parent::__construct(); 
            $stmt2 = $this->prepare("delete from user where id=?");  
            $stmt2->bind_param('s', $this->userId);  
            if($stmt2->execute()){
                PrintJSON("", "Delete parent ID: $prModel->id, and User ID: $this->userId Successfull!", 1);
            }
            $this->closeall($stmt2);  

           
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }


    public function viewParent(){
        try{ 
            $this->CheckParentId();
            $model=$this->PModel;
            parent::__construct();
            $stmt = $this->prepare("select * from user as u join parent as p on p.user_id=u.id  where p.id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Parent ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"User Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewAllParent(){
        try{  
            $model=$this->PModel;
            parent::__construct();
            $stmt = $this->prepare("select * from user as u join parent as p on p.user_id=u.id");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Parent Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Parent Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function getUserId(){
        parent::__construct();
        $prModel =$this->PModel; 
        $stmt = $this->prepare("select * from parent where id=?");  
        $stmt->bind_param('s', $prModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Parent ID: $prModel->id, Or User ID: $prModel->userid is not available!", 0);
            die(); 
        }else{ 
            foreach($rs as $k=>$v){
                $userid=$v['user_id'];
            }
          return $userid; 
        }
        $this->closeall($stmt); 
    }

    public function getDateCreate(){
        parent::__construct();
        $prModel =$this->PModel; 
        $stmt = $this->prepare("select * from user where id=?");  
        $stmt->bind_param('s', $prModel->userid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $createD=0;
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;   
    }

    public function CheckuserId(){ 
        parent::__construct(); 
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
    public function CheckVillageId(){ 
        parent::__construct(); 
        $this->setDB(adminschool_db);
        $prModel =$this->PModel; 
        $stmt = $this->prepare("select * from village where id=?");  
        $stmt->bind_param('s', $prModel->villageid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Village ID: $prModel->villageid is not In Database!", 0);
            die(); 
        }
        $this->closeall($stmt); 
    }

    public function CheckParentId(){  
        parent::__construct();
        $PrModel =$this->PModel;  
        if(isset($PrModel->userid)){ // update parent
            $stmt = $this->prepare("select * from parent where id=? and user_id=?");  
            $stmt->bind_param('ss', $PrModel->id, $PrModel->userid);
            $stmt->execute();   
            $rs = $stmt->get_result(); // get the mysqli result
            if(empty($rs->num_rows)){ 
                PrintJSON("", "parent ID: $PrModel->id Or User ID: $PrModel->userid, is not available!", 0);
                die(); 
            }
            $this->closeall($stmt);     
        }else{  // delete parent
        $stmt = $this->prepare("select * from parent where id=?");  
        $stmt->bind_param('s', $PrModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "parent ID: $PrModel->id, is not available!", 0);
            die(); 
        }
        $this->closeall($stmt); 
        }
    }

    function DeleteOldFile(){
        parent::__construct();
        $stmt=$this->prepare("select image from parent where id ={$this->PModel->id}"); 
        $stmt->execute();
        $rs = $stmt->get_result();
        if(!empty($rs->num_rows)){
            foreach($rs as $k=>$v){
                if(!empty($v['image'])){
                    @unlink(dir_images.$v['image']); 
                }
            } 
        }else{
            echo "no data";
        }
    }

    function getOldFile(){
        parent::__construct();
        $stmt=$this->prepare("select image from parent where id ={$this->PModel->id}"); 
        $stmt->execute();
        $imagename='';
        $rs = $stmt->get_result();
        if(!empty($rs->num_rows)){
            foreach($rs as $k=>$v){
                if(!empty($v['image'])){ 
                    $imagename=$v['image'];
                }
            } 
        } 
        return $imagename;
        
    }

}

?>