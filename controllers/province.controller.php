<?php 
include_once("../model/province.model.php");
include_once("base.controller.php");
class ProvinceController extends BASECONTROLLER{
    public $pvModel;
    function __construct($obj)
    {
        parent::__construct();
        $this->setDB(adminschool_db);
        $Pmodel=new ProvinceModel();
        $Pmodel->parseObject($obj);
        $v =[];
        $v = $Pmodel->ValidateAll();
        $v =array_merge($Pmodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->pvModel=$Pmodel;
    }

    public function createProvince(){
        try{
            $this->checkExitLevelname();
            $model=$this->pvModel; 
            $this->setDB(adminschool_db);
            $sql="insert into province(name,created_date,updated_date) values(?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sss',$model->name,$model->createdate,$model->updatedate);
            if( $stmt->execute()){
                PrintJSON([],"Create Province Name: $model->name Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateProvince(){
        try{
            $this->checkExitLevelname();
            $this->checkExitLevelId();
            $pmodel=$this->pvModel;
            $createD=$this->getDateCreate(); 
            $this->setDB(adminschool_db);
            $sql="update province set name=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$pmodel->name,$createD,$pmodel->updatedate, $pmodel->id);
                if( $stmt->execute()){
                    PrintJSON([],"update Province Name: $pmodel->name Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewProvince(){
        $this->checkExitLevelId();
        $model=$this->pvModel;
        try{  
            $this->setDB(adminschool_db);
            $stmt = $this->prepare("select * from province where id=?");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Province ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Province Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewAllProvince(){ 
        $model=$this->pvModel;
        try{  
            $this->setDB(adminschool_db);
            $stmt = $this->prepare("select * from province");   
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr[]= $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View All Province Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Province Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteProvince(){
        try{
            $this->checkExitLevelId();
            $pmodel=$this->pvModel;
            $this->setDB(adminschool_db);
            $sql="delete from province where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $pmodel->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Province ID: $pmodel->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitLevelId(){
        $this->setDB(adminschool_db);
        $sql="select id from province where id='".$this->pvModel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->pvModel->id;
            PrintJSON([],"Your Province ID: $id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitLevelname(){
        $this->setDB(adminschool_db);
        $sql="select name from province where name='".$this->pvModel->name."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(!empty($rs->num_rows)){
            $name=$this->pvModel->name;
            PrintJSON([],"your Province Name: $name already to create before", 0);
            die();
        }
    }

    public function getDateCreate(){
        $this->setDB(adminschool_db);
        $stmt = $this->prepare("select created_date from province where id=?");  
        $stmt->bind_param('s', $this->pvModel->id);
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