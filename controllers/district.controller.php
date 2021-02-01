<?php 
include_once("../model/district.model.php");
include_once("base.controller.php");
class DistrictController extends BASECONTROLLER{
    public $DistrictM;
    function __construct($obj)
    {
        parent::__construct();
        $Dmodel=new DistrictModel();
        $Dmodel->parseObject($obj);
        $v =[];
        $v = $Dmodel->ValidateAll();
        $v =array_merge($Dmodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->DistrictM=$Dmodel;
    }

    public function createDistrict(){
        try{ 
            $this->checkExitprovinceId();
            $this->checkExitdistrictname();
            $Dtmodel=$this->DistrictM;
            // print_r($Dtmodel);exit;
            parent::__construct();
            $sql="insert into district(name, province_id,created_date,updated_date) values(?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$Dtmodel->name,$Dtmodel->provinceid,$Dtmodel->createdate,$Dtmodel->updatedate);
            if($stmt->execute()){
                $name=$this->DistrictM->name;
                PrintJSON([],"Create district Name: $name Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateDistrict(){
        try{
            $this->checkExitProvinceId();
            $this->checkExitdistrictname();
            $this->checkExitDistrictId();

            parent::__construct();
            $SModel=$this->DistrictM;
            // print_r($SModel);exit;
            $createD=$this->getDateCreate(); 

            $sql="update district set name=?, province_id=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("sssss", $this->DistrictM->name,$this->DistrictM->provinceid,$createD,$this->DistrictM->updatedate,$this->DistrictM->id);
                if( $stmt->execute()){
                    $name=$this->DistrictM->name;
                    PrintJSON([],"update District Name: $name Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewDistrict(){
        try{    
            $this->checkExitDistrictId();
            $SModel=$this->DistrictM;
            parent::__construct();
            $stmt = $this->prepare("select * from district where id=?");  
            $stmt->bind_param('s', $SModel->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $districtArray = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $districtArray[] = $v;
                    }
                    $jsonObj='"Data":{'.json_encode($districtArray, true).',"Message":"Select Data Success Full","status":1}';
                    echo json_encode($jsonObj);
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"district Id: $SModel->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteDistrict(){
        try{
            $this->checkExitDistrictId();
            $SModel=$this->DistrictM;
            parent::__construct();
            $sql="delete from district where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $SModel->id);
            if($stmt->execute()){
                PrintJSON([],"Delete district ID: $SModel->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitDistrictId(){
        parent::__construct();
        $sql="select id from district where id='".$this->DistrictM->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->DistrictM->id;
            PrintJSON([],"Your district ID: $id It is not Valiable!", 0);
            die();
        }
    }
    function checkExitProvinceId(){
        parent::__construct();
        $sql="select * from province where id='".$this->DistrictM->provinceid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->DistrictM->provinceid;
            PrintJSON([],"Your province ID: $id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitdistrictname(){
        parent::__construct(); 
        $sql="select name from district where name='".$this->DistrictM->name."' and province_id='".$this->DistrictM->provinceid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
            if(!empty($rs->num_rows)){
                $name=$this->DistrictM->name;
                $provinceid=$this->DistrictM->provinceid;
                PrintJSON([],"your district Name: $name already to create before In province ID: $provinceid ", 0);
                die();
        }   
        $this->closeall($stmt);
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from district where id=?");  
        $stmt->bind_param('s', $this->DistrictM->id);
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