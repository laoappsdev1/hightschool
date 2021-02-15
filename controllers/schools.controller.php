<?php 

include_once ('base.controller.php'); 
include_once ('../model/schools.model.php');
class SchoolController extends BASECONTROLLER{ 

    public $schoolmodel; 

    public function __construct($object){
        parent::__construct(); 
        $this->setDB(adminschool_db);

        $smodel=new SchoolModel();  
        $smodel->parseObject($object);
        $v =[];
        $v = $smodel->ValidateAll();
        $v =array_merge($smodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        } 
        $this->schoolmodel = $smodel; 

    }

    public function  checkExistUsername(){
        $result = [];
        $model=$this->schoolmodel;
        $stmt = $this->prepare("select * from schools where username=?");
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
            $m = sprintf($m,$this->schoolmodel->username);
            PrintJSON([],$m,0);
            die();
        }

    }
    function checkValidateDbname(){
        $result = [];
        parent::__construct();
        $model=$this->schoolmodel;
        $stmt = $this->prepare("select * from schools where db_name=?");
        $stmt->bind_param("s", $model->dbname);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $data = [];
        while ($row = $rs->fetch_row()) {
            $result[]='Database Name already to create before!';
        }
        $this->closeall($stmt); 
        if(sizeof($result)>0){
            $m =' database name %s , already to create before!';
            $m = sprintf($m,$model->dbname);
            PrintJSON([],$m,0);
            die();
        }
    }

    function createNewSchoolDb(){ 
        $model = $this->schoolmodel; 
        $mysqli = new mysqli('localhost', 'root', '') or die( $mysqli->error ); 
        $mysqli->query( "CREATE DATABASE $model->dbname" ) or die( $mysqli->error );  
        $tables = $mysqli->query( "SHOW TABLES FROM hightschool_db" ) or die( $mysqli->error ); 
        
        while( $table = $tables->fetch_array() ): $TABLE = $table[0];  
            $mysqli->query( "CREATE TABLE $model->dbname.$TABLE LIKE hightschool_db.$TABLE" ) or die( $mysqli->error ); 
                if($TABLE==='village' || $TABLE==='district' || $TABLE==='province' ){   // ຕ້ອງການຂໍ້ມູນຂອງສາມຕາຕະລາງນີ້ເທົ່ານັ້ນ
                    $mysqli->query( "INSERT INTO $model->dbname.$TABLE SELECT * FROM hightschool_db.$TABLE" ) or die( $mysqli->error );
                }
        endwhile;  
    } 
    
    public function CreateUserSchool(){  
        try{
            $this->checkExistUsername();
            $this->checkValidateAdminschoolID();
            $this->checkValidateDbname();
            parent::__construct();
            $model = $this->schoolmodel; 
            if(!empty($model->img))
            {
                $img_name="school_".time().rand(100,999).".".getbase64_name($model->img); 
                base64_to_jpeg($model->img, dir_images.$img_name);    
                $model->img=$img_name;
            } 

            $model->password=$this->getPasswordHash($model->password);
            $sql="insert into schools
            (username,password,status,db_name,name,tel,image,email,address,adminschool_id,description,created_date,updated_date) 
            values(?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sssssssssssss',
                $model->username,
                $model->password,
                $model->status,
                $model->dbname,
                $model->name,
                $model->tel,
                $model->img,
                $model->email,
                $model->address,
                $model->adminschoolid,
                $model->description,
                $model->createdate,
                $model->updatedate
            );
        if($stmt->execute()){  
            $this->createNewSchoolDb();
            echo json_encode(array("message"=>"Create User schools ID: $stmt->insert_id Success","status"=>"1"));
        }
            $this->closeall($stmt); 
        }catch (Exception $e){
            print_r($e->getMessage());
        } 
    }

    function getOldpassword(){
        parent::__construct();
        $model=$this->schoolmodel;
        $sql="select password from schools";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $result=$stmt->get_result();
        foreach($result as $k=>$v){
            $pass=$v['password'];
        }
        $this->closeall($stmt);
        return $pass;
    }

    function getPasswordUpdateUser(){
        $model=$this->schoolmodel;
        if(strlen($model->password)>55){  
            $model->password=$this->getOldpassword();  
        }else{
            $model->password=$this->getPasswordHash($model->password); 
        } 
    }

    function getoldDatabasenameupdate(){
        parent::__construct();
        $model=$this->schoolmodel;
        $stmt=$this->prepare("select db_name from schools where id=?");
        $stmt->bind_param('s',$model->id);
        $stmt->execute();
        $result=$stmt->get_result();
        $db='';
            foreach($result as $k){
                $db=$k['db_name'];
            }
        $this->closeall($stmt);
        return $db;
    }

    public function UpdateUserSchool(){ 
        try{ 
            $this->CheckId();  
            $this->checkValidateAdminschoolID();
            $createD=$this->getOldDate(); 
            $model=$this->schoolmodel;
            $this->getPasswordUpdateUser();  
            $oldDb=$this->getoldDatabasenameupdate(); 
            if(!empty($this->TModel->img))
            { 
                $this->DeleteOldFile(); 
                $Img_name="school_".time().rand(100,999).".".getbase64_name($model->img); 
                base64_to_jpeg($model->img, dir_images.$Img_name);   
                $model->img=$Img_name;
            }else{
                $model->img=$this->getOldFile(); 
            }

            parent::__construct();

            $sql="update schools set 
            username=?,
            password=?,
            status=?,
            db_name=?,
            name=?,
            tel=?,
            image=?,
            email=?,
            address=?,
            adminschool_id=?,
            description=?,
            created_date=?,
            updated_date=? 
            where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('ssssssssssssss',
                $model->username,
                $model->password,
                $model->status,
                $oldDb,
                $model->name,
                $model->tel,
                $model->img,
                $model->email,
                $model->address,
                $model->adminschoolid,
                $model->description,
                $model->createdate,
                $model->updatedate,
                $model->id
            );
            $data=$stmt->execute();
            if($data){
                PrintJSON([],"Update User schools Id: $model->id Success Full",1);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function ViewUserSchool(){
        try{ 
            $this->CheckId();
            $model=$this->schoolmodel;
            parent::__construct();
            $stmt = $this->prepare("select * from schools where id=?");  
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
                        $json = "{\"Data\":$data, \"Message\": \"View User schools ID: $model->id Success Full\", \"Status\":\"1\"}";
                        echo $json; 
                        $this->closeall($stmt);
                        die;
                }else{
                    PrintJSON([],"User schools Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function ViewAllUserSchool(){
        try{  
            $model=$this->schoolmodel;
            parent::__construct();
            $stmt = $this->prepare("select * from schools");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                        {
                            $arr[]= $v;
                        }
                        $data=json_encode($arr);
                        $json = "{\"Data\":$data, \"Message\": \"View All User schools Success Full\", \"Status\":\"1\"}";
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

    public function DeleteUserSchool(){
        try{
            $this->CheckId();
            $model=$this->schoolmodel;
            parent::__construct(); 
            $stmt = $this->prepare("delete from schools where id=?");  
            $stmt->bind_param('s', $model->id); 
            if($stmt->execute()){
                PrintJSON([],"Delete User schools Id: $model->id Success Full",1);
                $this->closeall($stmt); 
            } 
            die;
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    function DeleteOldFile(){
        parent::__construct();
        $stmt=$this->prepare("select image from schools where id ={$this->schoolmodel->id}"); 
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
        $stmt=$this->prepare("select image from schools where id ={$this->schoolmodel->id}"); 
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

    public function getOldDate(){
        parent::__construct();
        $uModel =$this->schoolmodel; 
        $stmt = $this->prepare("select * from schools where id=?");  
        $stmt->bind_param('s', $uModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $createD=[];
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;  
    }
    public function checkValidateAdminschoolID(){
        parent::__construct();
        $model =$this->schoolmodel; 
        $stmt = $this->prepare("select * from adminschool where id=?");  
        $stmt->bind_param('s', $model->adminschoolid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Adminschool ID: $model->adminschoolid is not available!", 0);  
            die(); 
        } 
    }

    public function CheckId(){  
        $model =$this->schoolmodel; 
        $stmt = $this->prepare("select * from schools where id=?");  
        $stmt->bind_param('s', $model->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "user schools ID: $model->id is not available!", 0);
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