<?php 
include_once("../model/score.model.php");
include_once("base.controller.php");
class ScoreController extends BASECONTROLLER{
    public $scoremodel;
    function __construct($obj)
    {
        parent::__construct();
        $score=new ScoreModel();
        $score->parseObject($obj);
        $v =[];
        $v = $score->ValidateAll();
        $v =array_merge($score->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->scoremodel=$score;
    }

    public function createScore(){
        try{
            $this->checkExitFollowStudentId();
            $this->checkExitSemesterDetailID();
            $this->checkExitScoreStudent();
            $model=$this->scoremodel;
            parent::__construct();
            $sql="insert into score(score,follow_student_id,semester_detail_id,description,created_date,updated_date) values(?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssss',
                $model->score,
                $model->followstudentid,
                $model->semesterdetailid,
                $model->description,
                $model->createdate,
                $model->updatedate
            );
            if( $stmt->execute()){
                PrintJSON([],"Create Score ID: $stmt->insert_id Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    public function updateScore(){
        try{
            $this->checkExitScoreId();
            $this->checkExitFollowStudentId();
            $this->checkExitSemesterDetailID();
            // $this->checkExitScoreStudent();
            $model=$this->scoremodel;
            $createD=$this->getDateCreate();
            parent::__construct();
            $sql="update score set score=?,follow_student_id=?,semester_detail_id=?,description=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sssssss',
                $model->score,
                $model->followstudentid,
                $model->semesterdetailid,
                $model->description,
                $createD,
                $model->updatedate,
                $model->id
            );
                if( $stmt->execute()){
                    PrintJSON([],"update Score ID: $model->id Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewScore(){
        $this->checkExitScoreId();
        $model=$this->scoremodel;
        try{  
            parent::__construct();
            $stmt = $this->prepare("select * from score where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Score ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Score Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewAllScore(){ 
        $model=$this->scoremodel;
        try{  
            parent::__construct();
            $stmt = $this->prepare("select * from score");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View Score Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Score Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteScore(){
        try{
            $this->checkExitScoreId();
            $model=$this->scoremodel;
            parent::__construct();
            $sql="delete from score where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $model->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Score ID: $model->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }
 

    function checkExitScoreId(){
        parent::__construct();
        $model=$this->scoremodel;
        $sql="select id from score where id='".$model->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"Your Score ID: $model->id Not Valiable!", 0);
            die();
        }
    }
    function checkExitFollowStudentId(){
        parent::__construct();
        $model=$this->scoremodel;
        $sql="select id from follow_student where id='".$model->followstudentid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"Follow Student ID: $model->followstudentid Not Valiable!", 0);
            die();
        }
    }
    function checkExitSemesterDetailID(){
        parent::__construct();
        $model=$this->scoremodel;
        $sql="select id from semester_detail where id='".$model->semesterdetailid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){ 
            PrintJSON([],"Semester Detail ID: $model->semesterdetailid Not Valiable!", 0);
            die();
        }
    }

    function checkExitScoreStudent(){
        parent::__construct();
        $model=$this->scoremodel; 
            $sql="select id from score where semester_detail_id='".$model->semesterdetailid."' and follow_student_id='".$model->followstudentid."'";
            $stmt=$this->prepare($sql);
            $stmt->execute();
            $rs = $stmt->get_result(); // get the mysqli result
            if(!empty($rs->num_rows)){ 
                PrintJSON([],"Your score : $model->score already to create before!", 0);
                die(); 
        }
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from score where id=?");  
        $stmt->bind_param('s', $this->scoremodel->id);
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