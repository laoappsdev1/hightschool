<?php 
include_once("../model/follow.student.model.php");
include_once("base.controller.php");
class FollowStudentController extends BASECONTROLLER{
    public $followstudentmodel;
    function __construct($obj)
    {
        parent::__construct();
        $flsdmodel=new FollowStudentModel();
        $flsdmodel->parseObject($obj);
        $v =[];
        $v = $flsdmodel->ValidateAll();
        $v =array_merge($flsdmodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->followstudentmodel=$flsdmodel;
    }

    public function createFollowStudent(){
        try{
            $this->checkExitFollowTeacherId(); 
            $this->checkExitLevelStudentId();
            $model=$this->followstudentmodel;
            parent::__construct();
            $sql="insert into follow_student(level_student_id,follow_teacher_id,score_special,replacement,refer,created_date,updated_date) values(?,?,?,?,?,?,?)";
            $stmt= $this->prepare($sql);
            $stmt->bind_param('sssssss',
                $model->levelstudentid,
                $model->followteacherid,
                $model->scorespecial,
                $model->replacement,
                $model->refer,
                $model->createdate,
                $model->updatedate
            );
            if($stmt->execute()){
                PrintJSON([],"Create Follow Student ID: $stmt->insert_id Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    public function updateFollowStudent(){
        try{
            $this->checkExitFollowTeacherId(); 
            $this->checkExitLevelStudentId(); 
            $this->checkExitFollowStudentId(); 
            $model=$this->followstudentmodel;
            $createD=$this->getDateCreate();

            parent::__construct();
            $sql="update follow_student set level_student_id=?,follow_teacher_id=?,score_special=?,replacement=?,refer=?,created_date=?,updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssssss',
                $model->levelstudentid,
                $model->followteacherid,
                $model->scorespecial,
                $model->replacement,
                $model->refer,
                $createD,
                $model->updatedate,
                $model->id
            );
                if( $stmt->execute()){
                    PrintJSON([],"update Follow Student ID: $model->id Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewFollowStudent(){
        try{   
            $this->checkExitFollowStudentId();
            $model=$this->followstudentmodel;
            parent::__construct();
            $stmt = $this->prepare("select * from follow_student where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Follow Student ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Follow Student Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewAllFollowStudent(){
        try{    
            $model=$this->followstudentmodel;
            parent::__construct();
            $stmt = $this->prepare("select * from follow_student");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Follow Student Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Follow Student Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteFollowStudent(){
        try{
            $this->checkExitFollowStudentId();
            $model=$this->followstudentmodel;
            parent::__construct();
            $sql="delete from follow_student where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $model->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Follow Student ID: $model->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitFollowStudentId(){
        parent::__construct();
        $model=$this->followstudentmodel;
        $sql="select id from follow_student where id='".$model->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"Your Follow Student ID: $model->id It is not Valiable!", 0);
            die();
        }
    }
    function checkExitLevelStudentId(){
        parent::__construct();
        $model=$this->followstudentmodel;
        $sql="select id from level_student where id='".$model->levelstudentid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"Your Level Student ID: $model->levelstudentid It is not Valiable!", 0);
            die();
        }
    }
    function checkExitFollowTeacherId(){
        parent::__construct();
        $model=$this->followstudentmodel;
        $sql="select id from follow_teacher where id='".$model->followteacherid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"Your Follow Teacher ID: $model->followteacherid It is not Valiable!", 0);
            die();
        }
    }

    

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from follow_student where id=?");  
        $stmt->bind_param('s', $this->followstudentmodel->id);
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