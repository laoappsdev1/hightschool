<?php 

include_once ('base.controller.php');
include_once ('../model/teacher.model.php');
include_once ('../model/user.model.php');
class TeacherController extends BASECONTROLLER{ 

    public $userModel; 
    public $TModel; 
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
            // teacher
        $teachModel=new TeacherModel();  
        $teachModel->parseObject($object);
        $v2 =[];
        $v2 = $teachModel->ValidateAll();

        $v2 =array_merge($teachModel->validateId(),$v2);
        if(sizeof($v2)>0){
            echo json_encode($v2);
            die();
        }

        $this->userModel = $uModel;
        $this->TModel = $teachModel;  
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
    public function createTeacher(){  
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

            //create teacher
            parent::__construct(); 
            if(!empty($this->TModel->img))
            {
                $img_name="Timage_".time().rand(100,999).".".getbase64_name($this->TModel->img); 
                base64_to_jpeg($this->TModel->img, dir_images.$img_name);    
                $this->TModel->img=$img_name;
            } 

            $teachModel = $this->TModel;  
            $sql2="insert into teacher(profile,firstname,lastname,gender,village_id,dob,tel,image,email,user_id) values(?,?,?,?,?,?,?,?,?,?)";
            $stmt2 = $this->prepare($sql2);
            $stmt2->bind_param('ssssssssss',$teachModel->profile,$teachModel->firstname,$teachModel->lastname,$teachModel->gender,$teachModel->villageid,$teachModel->dob,$teachModel->tel,$teachModel->img,$teachModel->email,$this->userId);
            if($stmt2->execute()){ 
                PrintJSON([],"Create Teacher Firstname: $teachModel->firstname  Success Full",1);
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

    public function updateTeacher(){ 
        try{ 
            $this->CheckTeacherId();
            $this->CheckVillageId();    
            $createD=$this->getDateCreate(); 

            // //update user 
            parent::__construct();
            $model=$this->userModel; 
            $this->getPasswordUpdateUser(); 

            $sql="update user set username=?, password=?, token=?, usertype=?, created_date=?, updated_date=? where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('sssssss', $model->username,$model->password,$model->token,$model->usertype,$createD,$model->updatedate,$this->TModel->userid);
            $stmt->execute();
            $this->closeall($stmt);
            
            //update teacher
            parent::__construct();
            if(!empty($this->TModel->img))
            { 
                $this->DeleteOldFile(); 
                $Img_name="Timage_".time().rand(100,999).".".getbase64_name($this->TModel->img); 
                base64_to_jpeg($this->TModel->img, dir_images.$Img_name);   
                $this->TModel->img=$Img_name;
            }else{
                $this->TModel->img=$this->getOldFile(); 
            }
            $teachModel=$this->TModel;

            $sql2="update teacher set firstname=?, lastname=?, gender=?,village_id=?, dob=?, tel=?, email=?, user_id=? , profile=? , image=?  where id=?";
            $stmt2=$this->prepare($sql2);
            $stmt2->bind_param('sssssssssss', $teachModel->firstname,$teachModel->lastname,$teachModel->gender,$teachModel->villageid,$teachModel->dob,$teachModel->tel,$teachModel->email,$teachModel->userid,$teachModel->profile,$teachModel->img,$teachModel->id);
            
            if($stmt2->execute()){
                PrintJSON([],"Update Teacher Firstname: $teachModel->firstname Success Full",1);
            }

        }catch(Exception $e){
            print_r($e->getMessage());
        }
    } 

    public function deleteTeacher(){
        try{   
            $this->CheckTeacherId(); 
            $this->userId=$this->getUserId();
            $this->DeleteOldFile(); 

            // delete Teacher
            $teachModel=$this->TModel;
            parent::__construct(); 
            $stmt = $this->prepare("delete from teacher where id=?");  
            $stmt->bind_param('s', $teachModel->id); 
            $stmt->execute(); 
            $this->closeall($stmt); 

            // delete user
            $uModel=$this->userModel;
            parent::__construct(); 
            $stmt2 = $this->prepare("delete from user where id=?");  
            $stmt2->bind_param('s', $this->userId);  
            if($stmt2->execute()){
                PrintJSON("", "Delete Teacher ID: $teachModel->id, and User ID: $this->userId Successfull!", 1);
            }
            $this->closeall($stmt2);  

           
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }


    public function viewTeacher(){
        try{ 
            $this->CheckTeacherId();
            $model=$this->TModel;
            parent::__construct();
            $stmt = $this->prepare("select * from user as u join teacher as e on e.user_id=u.id  where e.id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Teacher ID: $model->id Success Full\", \"Status\":\"1\"}";
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

    public function viewAllTeacher(){
        try{  
            $model=$this->TModel;
            parent::__construct();
            $stmt = $this->prepare("select * from user as u join teacher as e on e.user_id=u.id");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Teacher Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"User  Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function getUserId(){
        parent::__construct();
        $teachModel =$this->TModel; 
        $stmt = $this->prepare("select * from teacher where id=?");  
        $stmt->bind_param('s', $teachModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Employee ID: $teachModel->id, Or User ID: $teachModel->userid is not available!", 0);
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
        $teachModel =$this->TModel; 
        $stmt = $this->prepare("select * from user where id=?");  
        $stmt->bind_param('s', $teachModel->userid);
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
        $teachModel =$this->TModel; 
        $stmt = $this->prepare("select * from village where id=?");  
        $stmt->bind_param('s', $teachModel->villageid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Village ID: $teachModel->villageid is not In Database!", 0);
            die(); 
        }
        $this->closeall($stmt); 
    }

    public function CheckTeacherId(){  
        parent::__construct();
        $teachModel =$this->TModel;  
        if(isset($teachModel->userid)){ // update teacher
            $stmt = $this->prepare("select * from teacher where id=? and user_id=?");  
            $stmt->bind_param('ss', $teachModel->id, $teachModel->userid);
            $stmt->execute();   
            $rs = $stmt->get_result(); // get the mysqli result
            if(empty($rs->num_rows)){ 
                PrintJSON("", "Teacher ID: $teachModel->id Or User ID: $teachModel->userid, is not available!", 0);
                die(); 
            }
            $this->closeall($stmt);     
        }else{  // delete teacher
        $stmt = $this->prepare("select * from teacher where id=?");  
        $stmt->bind_param('s', $teachModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Teacher ID: $teachModel->id, is not available!", 0);
            die(); 
        }
        $this->closeall($stmt); 
        }
    }

    function DeleteOldFile(){
        parent::__construct();
        $stmt=$this->prepare("select image from teacher where id ={$this->TModel->id}"); 
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
        $stmt=$this->prepare("select image from teacher where id ={$this->TModel->id}"); 
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