<?php 
include_once("../model/follow.teacher.model.php");
include_once("base.controller.php");
class FollowTeacherController extends BASECONTROLLER{
    public $followteacherModel;
    function __construct($obj)
    {
        parent::__construct();
        $FLT=new FollowTeachModel(); 
        $FLT->parseObject($obj); 
        
        $v =[];
        $v = $FLT->ValidateAll();

        $v =array_merge($FLT->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->followteacherModel=$FLT; 
    }

    public function createFollowTeacher(){
        try{ 
            $this->checkExitTeacherTimetableId(); 
            $model=$this->followteacherModel;
            // print_r($model);exit;
            parent::__construct();
            // echo $model->replacement;exit;
            $sql="insert into follow_teacher(teacher_timetable_id, refer, replacement, replacementdescription, created_date, updated_date) values(?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssss',
                $model->teachertimetableid,
                $model->refer,
                $model->replacement,
                $model->replacementdescription,
                $model->createdate,
                $model->updatedate
            );
            if($stmt->execute()){ 
                PrintJSON([],"Create Follow Teacher ID: $stmt->insert_id Success Fully!",1); 
            }
            echo  "sdfasdfasdf";
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateFollowTeacher(){
        try{
            $this->checkExitFollowTeacherId(); 
            $this->checkExitTeacherTimetableId(); 
            $model=$this->followteacherModel;
            $createD=$this->getDateCreate();

            parent::__construct();
            $sql="update follow_teacher set teacher_timetable_id=?, refer=?, replacement=?, replacementdescription=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sssssss',
                $model->teachertimetableid,
                $model->refer, 
                $model->replacement,
                $model->replacementdescription,
                $createD,
                $model->updatedate, 
                $model->id
            );

                if( $stmt->execute()){
                    PrintJSON([],"update Follow Teacher ID: $model->id Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewFollowTeacher(){
        try{  
            $this->checkExitFollowTeacherId(); 
            $model=$this->followteacherModel;
            parent::__construct();
            $stmt = $this->prepare("select * from follow_teacher where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Follow Teacher ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Follow Teacher ID: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    public function viewByTeacher(){
        try{   
            $model=$this->followteacherModel;
            parent::__construct();
            $condition='';

            if(!empty($model->teacherid)){
                $condition.="and tt.teacher_id='$model->teacherid'";
            }
            if(!empty($model->subjectid)){
                $condition.="and tt.subject_id='$model->subjectid'";
            }
            if(!empty($model->status)){
                $condition.="and tt.status='$model->status'";
            }
            
            $stmt = $this->prepare("select * from 
            follow_teacher as ft join teacher_timetable as tt 
            on ft.teacher_timetable_id=tt.id where ft.id>'0' $condition");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Follow Teacher Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Follow Teacher Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    public function viewAllFollowTeacher(){
        try{   
            $model=$this->followteacherModel;
            parent::__construct();
            $stmt = $this->prepare("select * from follow_teacher");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Follow Teacher Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Follow Teacher Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteFollowTeacher(){
        try{
            $this->checkExitFollowTeacherId();
            $model=$this->followteacherModel;
            parent::__construct();
            $sql="delete from follow_teacher where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $model->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Follow Teacher ID: $model->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitTeacherTimetableId(){
        parent::__construct();
        $sql="select id from teacher_timetable where id='".$this->followteacherModel->teachertimetableid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->followteacherModel->teachertimetableid;
            PrintJSON([],"Your Teacher Timetable ID: $id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitFollowTeacherId(){
        parent::__construct();
        $sql="select id from follow_teacher where id='".$this->followteacherModel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->followteacherModel->id;
            PrintJSON([],"Your Follow Teacher ID: $id It is not Valiable!", 0);
            die();
        }
    }


    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from follow_teacher where id=?");  
        $stmt->bind_param('s', $this->followteacherModel->id);
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