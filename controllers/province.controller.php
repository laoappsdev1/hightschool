<?php 
include_once("../model/province.model.php");
include_once("base.controller.php");
class ProvinceController extends BASECONTROLLER{
    public $pvModel;
    function __construct($obj)
    {
        parent::__construct();
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
            $pmodel=$this->pvModel;
            parent::__construct();
            $sql="insert into province(name,created_date,updated_date) values(?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sss',$pmodel->name,$pmodel->createdate,$pmodel->updatedate);
            if( $stmt->execute()){
                PrintJSON([],"Create Province Name: $pmodel->name Success Fully!",1);
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

            parent::__construct();
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
        $pmodel=$this->pvModel;
        try{  
            parent::__construct();
            $stmt = $this->prepare("select * from province where id=?");  
            $stmt->bind_param('s', $pmodel->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $LvArray = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $LvArray[] = $v;
                    }
                    $jsonObj='"Data":{'.json_encode($LvArray, true).',"Message":"Select Data Success Full","status":1}';
                    echo json_encode($jsonObj);
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Province Id: $pmodel->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteProvince(){
        try{
            $this->checkExitLevelId();
            $pmodel=$this->pvModel;
            parent::__construct();
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
        parent::__construct();
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
        parent::__construct();
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
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from province where id=?");  
        $stmt->bind_param('s', $this->pvModel->id);
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