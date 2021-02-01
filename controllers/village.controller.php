<?php 
include_once("../model/village.model.php");
include_once("base.controller.php");
class VillageController extends BASECONTROLLER{
    public $vlModel;
    function __construct($obj)
    {
        parent::__construct();
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
            // print_r($Dtmodel);exit;
            parent::__construct();
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
            $SModel=$this->vlModel;
            parent::__construct();
            $stmt = $this->prepare("select * from village where id=?");  
            $stmt->bind_param('s', $SModel->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $villagetArray = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $villagetArray[] = $v;
                    }
                    $jsonObj='"Data":{'.json_encode($villagetArray, true).',"Message":"Select Data Success Full","status":1}';
                    echo json_encode($jsonObj);
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Village Id: $SModel->id Can't valiable", 0);
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
        parent::__construct(); 
        // print_r($this->vlModel);exit;
        $sql="select name from village where name='".$this->vlModel->name."' and district_id='".$this->vlModel->districtid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
            if(!empty($rs->num_rows)){
                $name=$this->vlModel->name;
                $Did=$this->vlModel->districtid;
                PrintJSON([],"This Village Name: $name already to create before In District ID: $Did ", 0);
                die();
        }   
        $this->closeall($stmt);
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from village where id=?");  
        $stmt->bind_param('s', $this->vlModel->id);
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