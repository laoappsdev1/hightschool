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
            $model=$this->DistrictM; 
            parent::__construct();
            $sql="insert into district(name, province_id,created_date,updated_date) values(?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$model->name,$model->provinceid,$model->createdate,$model->updatedate);
            if($stmt->execute()){ 
                PrintJSON([],"Create district Name: $model->name Success Fully!",1);
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
            $model=$this->DistrictM; 
            $createD=$this->getDateCreate(); 

            $sql="update district set name=?, province_id=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param("sssss", $model->name,$model->provinceid,$createD,$model->updatedate,$model->id);
                if( $stmt->execute()){ 
                    PrintJSON([],"update District Name: $model->name Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewDistrict(){
        try{    
            $this->checkExitDistrictId();
            $model=$this->DistrictM;
            parent::__construct();
            $stmt = $this->prepare("
                select 
                p.id province_id, 
                p.name province, 
                d.id district_id, 
                d.name district 
                from district as d join province as p 
                on d.province_id=p.id
                where d.id=?"
            );  
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
                    $json = "{\"Data\":$data, \"Message\": \"View District ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"district Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewAllDistrict(){
        try{     
            $model=$this->DistrictM;
            parent::__construct();
            $keywords='';
            $search=trim($model->keyword);
                if(!empty(strlen($search))){  //trim() ແມ່ນ function ຕັດ space ທັງຫນ້າ ທັງຫຼັງ
                    $keywords.="and 
                    (
                        d.name like '%".$search."%'
                        or p.name like '%".$search."%'
                    )";
                }
            $stmt = $this->prepare("
            select 
                p.id province_id, 
                p.name province, 
                d.id district_id, 
                d.name district 
                from district as d join province as p 
                on d.province_id=p.id
                where d.id>0
                $keywords
            ");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All District Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"district Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteDistrict(){
        try{
            $this->checkExitDistrictId();
            $model=$this->DistrictM;
            parent::__construct();
            $sql="delete from district where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $model->id);
            if($stmt->execute()){
                PrintJSON([],"Delete district ID: $model->id Success Full!",1);
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
        $model=$this->DistrictM;
        $sql="select name from district where name='".$model->name."' and province_id='".$model->provinceid."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
            if(!empty($rs->num_rows)){
                $name=$model->name;
                $provinceid=$model->provinceid;
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
        $createD=0;
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;   
    }
}

?>