<?php 
include_once("../model/semester.model.php");
include_once("base.controller.php");
class SemesterController extends BASECONTROLLER{
    public $SMmodel;
    function __construct($obj)
    {
        parent::__construct();
        $Smodel=new SemesterModel();
        $Smodel->parseObject($obj);
        $v =[];
        $v = $Smodel->ValidateAll();
        $v =array_merge($Smodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->SMmodel=$Smodel;
    }

    public function createSemester(){
        try{
            $this->checkExitSmesterName();
            $semesterM=$this->SMmodel;
            parent::__construct();
            $sql="insert into semester(name,created_date,updated_date) values(?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sss',$semesterM->name,$semesterM->createdate,$semesterM->updatedate);
            if( $stmt->execute()){
                PrintJSON([],"Create Semester Name: $semesterM->name; Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateSemester(){
        try{
            $this->checkExitSmesterName();
            $this->checkExitSemesterId();
            $smerterM=$this->SMmodel;
            $createD=$this->getDateCreate();

            parent::__construct();
            $sql="update semester set name=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$smerterM->name,$createD,$smerterM->updatedate, $smerterM->id);
                if( $stmt->execute()){
                    $name=$this->SMmodel->name;
                    PrintJSON([],"update Semester Name: $name Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewSemester(){
        $this->checkExitSemesterId(); 
        try{  
            parent::__construct();
            $id=$this->SMmodel->id;
            $stmt = $this->prepare("select * from Semester where id=?");  
            $stmt->bind_param('s', $id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $SmtArray = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $SmtArray[] = $v;
                    }
                    $jsonObj='"Data":{'.json_encode($SmtArray, true).',"Message":"Select Data Success Full","status":1}';
                    echo json_encode($jsonObj);
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Semester Id: $id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteSemester(){
        try{
            $this->checkExitSemesterId();
            $Smodel=$this->SMmodel;
            parent::__construct();
            $sql="delete from semester where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $Smodel->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Semester ID: $Smodel->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitSemesterId(){
        parent::__construct();
        $sql="select id from semester where id='".$this->SMmodel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->SMmodel->id;
            PrintJSON([],"Your Semester ID: $id It is not Valiable!", 0);
            die();
        }
        $this->closeall($stmt);
    }

    function checkExitSmesterName(){
        parent::__construct();
        $sql="select name from semester where name='".$this->SMmodel->name."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(!empty($rs->num_rows)){
            $name=$this->SMmodel->name;
            PrintJSON([],"your Semester Name: $name already to create before", 0);
            die();
        }
        $this->closeall($stmt);
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from semester where id=?");  
        $stmt->bind_param('s', $this->SMmodel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $createD=[];
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        $this->closeall($stmt);
        return $createD;   
    }
}

?>