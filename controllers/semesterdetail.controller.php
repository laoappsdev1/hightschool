<?php 
include_once("../model/SemesterDetail.model.php");
include_once("base.controller.php");
class SemesterDetailController extends BASECONTROLLER{
    public $smtdModel;
    function __construct($obj)
    {
        parent::__construct();
        $smtdM=new SemesterDetailModel();
        $smtdM->parseObject($obj);
        $v =[];
        $v = $smtdM->ValidateAll();
        $v =array_merge($smtdM->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->smtdModel=$smtdM;
    }

    public function createSemesterDetail(){
        try{ 
            $this->checkExitSemesterId();
            $this->checkExitSemesterDetailMonth();
            $SMTD=$this->smtdModel;
            parent::__construct();
            $sql="insert into semester_detail(month, semester_id,created_date,updated_date) values(?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$SMTD->month,$SMTD->semesterid,$SMTD->createdate,$SMTD->updatedate);
            if($stmt->execute()){
                $month=$SMTD->month;
                PrintJSON([],"Create Semester Detail Month: $month Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateSemesterDetail(){
        try{
            $this->checkExitSemesterId();
            $this->checkExitSemesterDetailMonth();
            $this->checkExitSemesterDetailId();

            parent::__construct();
            // $SMTD=$this->smtdModel;
            // print_r($SModel);exit;1
            $createD=$this->getDateCreate(); 

            $sql="update semester_detail set month=?, semester_id=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("sssss", $this->smtdModel->month,$this->smtdModel->semesterid,$createD,$this->smtdModel->updatedate,$this->smtdModel->id);
                if( $stmt->execute()){
                    $month=$this->smtdModel->month;
                    PrintJSON([],"update Semester Detail Month: $month Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewSemesterDetail(){
        try{    
            $this->checkExitSemesterDetailId();
            $model=$this->smtdModel;
            parent::__construct();
            $stmt = $this->prepare("select * from semester_detail where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Semester Detail ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Semester Detail Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewAllSemesterDetail(){
        try{     
            $model=$this->smtdModel;
            parent::__construct();
            $stmt = $this->prepare("select * from semester_detail");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Semester Detail Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Semester Detail Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteSemesterDetail(){
        try{
            $this->checkExitSemesterDetailId();
            $SMTD=$this->smtdModel;
            parent::__construct();
            $sql="delete from semester_detail where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $SMTD->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Semester Detail ID: $SMTD->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitSemesterDetailId(){
        parent::__construct();
        $sql="select id from semester_detail where id='".$this->smtdModel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->smtdModel->id;
            PrintJSON([],"Your Semester Detail ID: $id It is not Valiable!", 0);
            die();
        }
    }
    function checkExitSemesterId(){
        parent::__construct();
        $sql="select * from semester where id='".$this->smtdModel->semesterid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->smtdModel->semesterid;
            PrintJSON([],"Your semester ID: $id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitSemesterDetailMonth(){
        parent::__construct(); 
        $sql="select month from semester_detail where month='".$this->smtdModel->month."' and semester_id='".$this->smtdModel->semesterid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
            if(!empty($rs->num_rows)){
                $month=$this->smtdModel->month;
                $semester_id=$this->smtdModel->semesterid;
                PrintJSON([],"your Semester Detail Month: $month already to create before In Semester ID: $semester_id ", 0);
                die();
        }   
        $this->closeall($stmt);
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from semester_detail where id=?");  
        $stmt->bind_param('s', $this->smtdModel->id);
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