<?php 
include_once("../model/subject.model.php");
include_once("base.controller.php");
class SubjectController extends BASECONTROLLER{
    public $SJModel;
    function __construct($obj)
    {
        parent::__construct();
        $subjectmodel=new SubjectModel();
        $subjectmodel->parseObject($obj);
        $v =[];
        $v = $subjectmodel->ValidateAll();
        $v =array_merge($subjectmodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->SJModel=$subjectmodel;
    }

    public function createSubject(){
        try{ 
            $this->checkExitLevelId();
            $this->checkExitSubjectname();
            $Smodel=$this->SJModel;
            parent::__construct();
            $sql="insert into subject(name, level_id,created_date,updated_date) values(?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$Smodel->name,$Smodel->levelid,$Smodel->createdate,$Smodel->updatedate);
            if($stmt->execute()){
                PrintJSON([],"Create Subject ID:$stmt->insert_id Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateSubject(){
        try{
            $this->checkExitLevelId();
            $this->checkExitSubjectname();
            $this->checkExitSubjectId();

            parent::__construct();
            $SModel=$this->SJModel;
            // print_r($SModel);exit;
            $createD=$this->getDateCreate(); 

            $sql="update subject set name=?, level_id=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("sssss", $this->SJModel->name,$this->SJModel->levelid,$createD,$this->SJModel->updatedate,$this->SJModel->id);
                if( $stmt->execute()){
                    $id=$this->SJModel->id;
                    PrintJSON([],"update Suject ID: $id Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewSubject(){
        try{    
            $this->checkExitSubjectId();
            $SModel=$this->SJModel;
            parent::__construct();
            $stmt = $this->prepare("select * from subject where id=?");  
            $stmt->bind_param('s', $SModel->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $SubjectArray = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $SubjectArray[] = $v;
                    }
                    $jsonObj='"Data":{'.json_encode($SubjectArray, true).',"Message":"Select Data Success Full","status":1}';
                    echo json_encode($jsonObj);
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Subject Id: $SModel->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteSubject(){
        try{
            $this->checkExitSubjectId();
            $SModel=$this->SJModel;
            parent::__construct();
            $sql="delete from subject where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $SModel->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Subject ID: $SModel->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitSubjectId(){
        parent::__construct();
        $sql="select id from subject where id='".$this->SJModel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->SJModel->id;
            PrintJSON([],"Your Subject ID: $id It is not Valiable!", 0);
            die();
        }
    }
    function checkExitLevelId(){
        parent::__construct();
        $sql="select * from level where id='".$this->SJModel->levelid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->SJModel->levelid;
            PrintJSON([],"Your Level ID: $id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitSubjectname(){
        parent::__construct(); 
        $sql="select name from subject where name='".$this->SJModel->name."' and level_id='".$this->SJModel->levelid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
            if(!empty($rs->num_rows)){
                $name=$this->SJModel->name;
                $levelid=$this->SJModel->levelid;
                PrintJSON([],"your Subject Name: $name already to create before In Lavel ID: $levelid ", 0);
                die();
        }   
        $this->closeall($stmt);
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from subject where id=?");  
        $stmt->bind_param('s', $this->SJModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $createD=[];
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;   
    }
}

?>