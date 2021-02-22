<?php 
include_once("../model/years.model.php");
include_once("base.controller.php");
class YearsController extends BASECONTROLLER{
    public $Ymodel;
    function __construct($obj)
    {
        parent::__construct();
        $yearsmodel=new YearsModel();
        $yearsmodel->parseObject($obj);
        $v =[];
        $v = $yearsmodel->ValidateAll();
        $v =array_merge($yearsmodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->Ymodel=$yearsmodel;
    }

    public function createYears(){
        try{
            $this->checkExitYearschool();
            $model=$this->Ymodel;
            parent::__construct();
            $sql="insert into years(series,year,schoolyear,created_date,updated_date) values(?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sssss',
            $model->series,
            $model->year,
            $model->schoolyear,
            $model->createdate,
            $model->updatedate);
            if( $stmt->execute()){
                PrintJSON([],"Create School Year: $model->schoolyear Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateYears(){
        try{
            $this->checkExitYearschool();
            $this->checkExitYearsId();
            $model=$this->Ymodel;
            $createD=$this->getDateCreate();

            parent::__construct();
            $sql="update years set series=?, year=?, schoolyear=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssss',
            $model->series,
            $model->year,
            $model->schoolyear,
            $model->createdate,
            $model->updatedate,
            $model->id);

                if($stmt->execute()){
                    PrintJSON([],"update School Year : $model->schoolyear Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewYears(){
        try{  
            $this->checkExitYearsId();
            $model=$this->Ymodel;
            parent::__construct();
            $stmt = $this->prepare("select * from years where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Years ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Years Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewAllYears(){ 
        try{   
            $model=$this->LModel;
            parent::__construct();
            $stmt = $this->prepare("select * from years");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Years Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Years Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteYears(){
        try{
            $this->checkExitYearsId();
            $model=$this->Ymodel;
            parent::__construct();
            $sql="delete from years where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $model->id);
            if($stmt->execute()){
                PrintJSON([],"Delete School Years ID: $model->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitYearsId(){
        parent::__construct();
        $sql="select id from years where id='".$this->Ymodel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->Ymodel->id;
            PrintJSON([],"Your School Year ID: $id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitYearschool(){
        parent::__construct();
        $sql="select name from years where year='".$this->Ymodel->year."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(!empty($rs->num_rows)){
            $years=$this->Ymodel->year;
            PrintJSON([],"your Year: $years already to create before", 0);
            die();
        }
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from years where id=?");  
        $stmt->bind_param('s', $this->Ymodel->id);
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