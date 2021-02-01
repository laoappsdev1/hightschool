<?php 
include_once("../model/classroom.model.php");
include_once("base.controller.php");
class ClassRoomController extends BASECONTROLLER{
    public $crModel;
    function __construct($obj)
    {
        parent::__construct();
        $cmodel=new ClassRoomModel();
        $cmodel->parseObject($obj);
        $v =[];
        $v = $cmodel->ValidateAll();
        $v =array_merge($cmodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->crModel=$cmodel;
    }

    public function createClassroom(){
        try{ 
            $this->checkExitLevelId();
            $this->checkExitClassroomNumber();
            
            $classmodel=$this->crModel;
            parent::__construct();
            $sql="insert into classroom(class_number, level_id,created_date,updated_date) values(?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$classmodel->classnumber,$classmodel->levelid,$classmodel->createdate,$classmodel->updatedate);
            if($stmt->execute()){
                PrintJSON([],"Create Class Room Number:$classmodel->classnumber Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateClassroom(){
        try{
            $this->checkExitLevelId();
            $this->checkExitClassroomNumber();
            $this->checkExitClassroomId();
            $createD=$this->getDateCreate(); 

            parent::__construct();
            $classmodel=$this->crModel;
            // print_r($classmodel);exit;

            $sql="update classroom set class_number=?, level_id=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("sssss", $this->crModel->classnumber,$this->crModel->levelid,$createD,$this->crModel->updatedate,$this->crModel->id);
                if( $stmt->execute()){
                    $id=$this->crModel->id;
                    PrintJSON([],"update Class Room Number: $classmodel->classnumber Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewClassroom(){
        try{    
            $this->checkExitClassroomId();
            $classmodel=$this->crModel;
            parent::__construct();
            $stmt = $this->prepare("select * from classroom where id=?");  
            $stmt->bind_param('s', $classmodel->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $classArr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $classArr[] = $v;
                    }
                    $jsonObj='"Data":{'.json_encode($classArr, true).',"Message":"Select Data Success Full","status":1}';
                    echo json_encode($jsonObj);
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"class room Id: $classmodel->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteClassroom(){
        try{
            $this->checkExitClassroomId();
            $classmodel=$this->crModel;
            parent::__construct();
            $sql="delete from classroom where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $classmodel->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Class Room ID: $classmodel->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitClassroomId(){
        parent::__construct();
        $sql="select id from classroom where id='".$this->crModel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->crModel->id;
            PrintJSON([],"Your Class Room ID: $id It is not Valiable!", 0);
            die();
        }
    }
    function checkExitLevelId(){
        parent::__construct();
        $sql="select * from level where id='".$this->crModel->levelid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->crModel->levelid;
            PrintJSON([],"Your Level ID: $id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitClassroomNumber(){
        parent::__construct(); 
        // $sql="select * from classroom where class_number='".$this->crModel->classnumber."' and level_id='".$this->crModel->levelid."'";
        $sql="select * from classroom where class_number='".$this->crModel->classnumber."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
            if(!empty($rs->num_rows)){
                $classnumber=$this->crModel->classnumber;
                // $levelid=$this->crModel->levelid;
                // PrintJSON([],"Your Class Number: $classnumber already to create before In Lavel ID: $levelid ", 0);
                PrintJSON([],"Your Class Number: $classnumber already to create before", 0);
                die();
        }   
        $this->closeall($stmt);
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from classroom where id=?");  
        $stmt->bind_param('s', $this->crModel->id);
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