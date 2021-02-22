<?php 
include_once("../model/teacher.timetable.model.php");
include_once("base.controller.php");
class TeacherTimetableController extends BASECONTROLLER{
    public $TTmodel;
    function __construct($obj)
    {
        parent::__construct();
        $TTmodel=new TeacherTimetableModel();
        $TTmodel->parseObject($obj);
        $v =[];
        $v = $TTmodel->ValidateAll();
        $v =array_merge($TTmodel->validateId(),$v);
         
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->TTmodel=$TTmodel;
    }

    public function createTeacherTimetable(){
        try{
            $this->checkExitTeacherId();
            $this->checkExitClassroomId();
            $this->checkExitSubjectId();
            $model=$this->TTmodel;
            parent::__construct();
            $sql="insert into teacher_timetable(date,time_start,time_end,status,exam,teacher_id,classroom_id,subject_id,description,created_date,updated_date) values(?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sssssssssss', 
            $model->date, 
            $model->timestart, 
            $model->timeend, 
            $model->status, 
            $model->exam, 
            $model->teacherid, 
            $model->classroomid, 
            $model->subjectid, 
            $model->description, 
            $model->createdate, 
            $model->updatedate
            );
            if($stmt->execute()){
                PrintJSON([],"Create Teacher Timetable ID: $stmt->insert_id Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function updateTeacherTimetable(){
        try{ 
            
            $model=$this->TTmodel;
            $this->checkExitTeacherTimetableId();
            $createD=$this->getDateCreate();

            parent::__construct();
            $sql="update teacher_timetable set date=?,time_start=?,time_end=?,status=?,exam=?,teacher_id=?,classroom_id=?,subject_id=?,description=?,created_date=?,updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssssssssss', 
                $model->date, 
                $model->timestart, 
                $model->timeend, 
                $model->status, 
                $model->exam, 
                $model->teacherid, 
                $model->classroomid, 
                $model->subjectid, 
                $model->description, 
                $createD, 
                $model->updatedate,
                $model->id
            );
                if( $stmt->execute()){
                    PrintJSON([],"update Tearch timetable ID: $model->id Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewTeacherTimetable(){
        $this->checkExitTeacherTimetableId();
        $model=$this->TTmodel;
        try{  
            parent::__construct();
            $stmt = $this->prepare("select * from teacher_timetable where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Teacher Timetable ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                } 
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewforteacher(){ 
        try{  
            $model=$this->TTmodel;
            $quadition='';
            if(!empty($model->teacherid)){
                $quadition.="and teacher_id='$model->teacherid'";
            }
            parent::__construct(); 
            $now=date('Y-m-d');
            $stmt = $this->prepare("select * from teacher_timetable where date > '$now' $quadition");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Teacher Timetable Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"teacher ID:$model->teacherid, don not have timetable!",'0');
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    public function viewAllTeacherTimetable(){ 
        $model=$this->TTmodel;
        try{  
            parent::__construct();
            $stmt = $this->prepare("select * from teacher_timetable");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Teacher Timetable Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                } 
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteTeacherTimetable(){
        try{
            $this->checkExitTeacherTimetableId();
            $model=$this->TTmodel;
            parent::__construct();
            $sql="delete from teacher_timetable where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $model->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Teacher Table ID: $model->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitTeacherTimetableId(){
        parent::__construct();
        $model=$this->TTmodel;
        $sql="select id from teacher_timetable where id='".$model->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON([],"Teacher Timetable ID: $model->id It is not Valiable!", 0);
            die();
        }
    }
    function checkExitTeacherId(){
        parent::__construct();
        $model=$this->TTmodel;
        $sql="select id from teacher where id='".$model->teacherid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON([],"Teacher ID: $model->teacherid It is not Valiable!", 0);
            die();
        }
    }
    function checkExitClassroomId(){
        parent::__construct();
        $model=$this->TTmodel;
        $sql="select id from classroom where id='".$model->classroomid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON([],"Class Room ID: $model->classroomid It is not Valiable!", 0);
            die();
        }
    }
    function checkExitSubjectId(){
        parent::__construct();
        $model=$this->TTmodel;
        $sql="select id from subject where id='".$model->subjectid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON([],"Subject ID: $model->subjectid It is not Valiable!", 0);
            die();
        }
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from teacher_timetable where id=?");  
        $stmt->bind_param('s', $this->TTmodel->id);
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