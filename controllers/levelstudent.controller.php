<?php 
include_once("../model/level.student.model.php");
include_once("base.controller.php");
class LevelStudentController extends BASECONTROLLER{
    public $levelstudent;
    function __construct($obj)
    {
        parent::__construct();
        $levelstudent=new LevelStudentModel();
        $levelstudent->parseObject($obj);
        $v =[];
        $v = $levelstudent->ValidateAll();
        $v =array_merge($levelstudent->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->levelstudent=$levelstudent;
    }

    public function createLevelStudent(){
        try{
            // $this->checkExitLevelStudentCreate();
            $this->checkExitStudentId();
            $this->checkExitLevelId();
            $this->checkExitClassroomId();
            $model=$this->levelstudent;
            parent::__construct();
            $sql="insert into level_student(level_id,student_id,classroom_id,status,description,created_date,updated_date) values(?,?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sssssss',
                $model->levelid,
                $model->studentid,
                $model->classroomid,
                $model->status,
                $model->description,
                $model->createdate,
                $model->updatedate
            );
            if($stmt->execute()){
                PrintJSON([],"Create Level Student ID: $stmt->insert_id Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateLevelStudent(){
        try{
            $this->checkExitLevelStudentId();
            $this->checkExitStudentId();
            $this->checkExitLevelId();
            $this->checkExitClassroomId();

            $model=$this->levelstudent;
            $createD=$this->getDateCreate();
            parent::__construct();
            $sql="update level_student set level_id=?, student_id=?, classroom_id=?, status=?, description=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssssss',
            $model->levelid,
            $model->studentid,
            $model->classroomid,
            $model->status,
            $model->description,
            $createD,
            $model->updatedate,
            $model->id
        );
                if( $stmt->execute()){
                    PrintJSON([],"update Level Student ID: $model->id Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewLevelStudent(){
        $this->checkExitLevelStudentId();
        $model=$this->levelstudent;
        try{  
            parent::__construct();
            $stmt = $this->prepare("select * from level_student where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Level Student ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Level student Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    public function viewAllLevelStudent(){ 
        $model=$this->levelstudent;
        try{  
            parent::__construct();
            $stmt = $this->prepare("select * from level_student");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View Level Student Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Level student Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteLevelStudent(){
        try{
            $this->checkExitLevelStudentId();
            $model=$this->levelstudent; 
            parent::__construct();
            $sql="delete from level_student where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $model->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Level Student ID: $model->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    // function checkExitLevelStudentCreate(){
    //     parent::__construct();
    //     $model=$this->levelstudent;
    //     $sql="select id from level_student where id='".$model->id."'";
    //     $stmt=$this->prepare($sql);
    //     $stmt->execute();
    //     $rs = $stmt->get_result(); // get the mysqli result
    //     if(empty($rs->num_rows)){ 
    //         PrintJSON([],"Your Level Student ID: $model->id It is not Valiable!", 0);
    //         die();
    //     }
    // }
    
    function checkExitLevelStudentId(){
        parent::__construct();
        $model=$this->levelstudent;
        $sql="select id from level_student where id='".$model->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"Your Level Student ID: $model->id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitLevelId(){
        parent::__construct();
        $model=$this->levelstudent;
        $sql="select id from level where id='".$model->levelid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"Your Level ID: $model->levelid It is not Valiable!", 0);
            die();
        }
    }

    function checkExitStudentId(){
        parent::__construct();
        $model=$this->levelstudent;
        $sql="select id from student where id='".$model->studentid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"your Level Student ID: $model->studentid, not Valiable!", 0);
            die();
        }
    }

    function checkExitClassroomId(){
        parent::__construct();
        $model=$this->levelstudent;
        $sql="select id from classroom where id='".$model->classroomid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"your Classroom ID: $model->classroomid, not Valiable!", 0);
            die();
        }
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from level_student where id=?");  
        $stmt->bind_param('s', $this->levelstudent->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $createD=0;
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;   
    }
}

?>