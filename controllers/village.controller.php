<?php 
include_once("../model/village.model.php");
include_once("base.controller.php");
class VillageController extends BASECONTROLLER{
    public $vlModel;
    function __construct($obj)
    {
        parent::__construct();
        $this->setDB(adminschool_db);
        $villageM=new VillageModel();
        $villageM->parseObject($obj);
        $v =[];
        $v = $villageM->ValidateAll();
        $v =array_merge($villageM->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->vlModel=$villageM;
    }

    public function createVillage(){
        try{ 
            $this->checkExitProvinceId_And_DistrictId();
            $this->checkExitVillagename();
            $Vmodel=$this->vlModel; 
           
            $this->setDB(adminschool_db);
            $sql="insert into village(name, district_id,created_date,updated_date) values(?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$Vmodel->name,$Vmodel->districtid,$Vmodel->createdate,$Vmodel->updatedate);
            if($stmt->execute()){
                $name=$this->vlModel->name;
                PrintJSON([],"Create Village Name: $name Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateVillage(){
        try{
            $this->checkExitProvinceId_And_DistrictId();
            $this->checkExitVillagename();
            $this->checkExitVillageId(); 
            parent::__construct();
            $this->setDB(adminschool_db);
            $SModel=$this->vlModel;
            // print_r($SModel);exit;
            $createD=$this->getDateCreate(); 

            $sql="update village set name=?, district_id=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("sssss", $this->vlModel->name,$this->vlModel->districtid,$createD,$this->vlModel->updatedate,$this->vlModel->id);
                if( $stmt->execute()){
                    $name=$this->vlModel->name;
                    PrintJSON([],"update Village Name: $name Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewVillage(){
        try{    
            $this->checkExitVillageId();
            $model=$this->vlModel;
            parent::__construct();
            $this->setDB(adminschool_db);
            $stmt = $this->prepare("select * from village where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Village ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Village Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    public function viewAllVillage(){
        try{     
            $model=$this->vlModel;
            parent::__construct();
            $this->setDB(adminschool_db);
            $stmt = $this->prepare("select * from village");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Village Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Village Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteVillage(){
        try{
            $this->checkExitVillageId();
            $SModel=$this->vlModel;
            parent::__construct();
            $this->setDB(adminschool_db);
            $sql="delete from village where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $SModel->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Village ID: $SModel->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitVillageId(){
        parent::__construct();
        $this->setDB(adminschool_db);
        $sql="select id from village where id='".$this->vlModel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->vlModel->id;
            PrintJSON([],"Your Village ID: $id It is not Valiable!", 0);
            die();
        }
    }
    function checkExitProvinceId_And_DistrictId(){
        parent::__construct();
        $this->setDB(adminschool_db);
        $sql="select * from district where id='".$this->vlModel->districtid."' and province_id='".$this->vlModel->provinceid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $pid=$this->vlModel->provinceid;
            $did=$this->vlModel->districtid;
            PrintJSON([],"Your Province ID: $pid Or District ID: $did  It is not Valiable!", 0);
            die();
        }
    }

    function checkExitVillagename(){
        $model=$this->vlModel;
        parent::__construct(); 
        $this->setDB(adminschool_db); 
        $sql="select name from village where name='".$model->name."' and district_id='".$model->districtid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
            if(!empty($rs->num_rows)){
                $name=$model->name;
                $Did=$model->districtid;
                PrintJSON([],"This Village Name: $name already to create before In District ID: $Did ", 0);
                die();
        }   
        $this->closeall($stmt);
    }

    public function getDateCreate(){
        parent::__construct(); 
        $this->setDB(adminschool_db);
        $stmt = $this->prepare("select created_date from village where id=?");  
        $stmt->bind_param('s', $this->vlModel->id);
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