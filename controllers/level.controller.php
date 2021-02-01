<?php 
include_once("../model/level.model.php");
include_once("base.controller.php");
class LevelController extends BASECONTROLLER{
    public $LModel;
    function __construct($obj)
    {
        parent::__construct();
        $levelmodel=new LevelModel();
        $levelmodel->parseObject($obj);
        $v =[];
        $v = $levelmodel->ValidateAll();
        $v =array_merge($levelmodel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
        $this->LModel=$levelmodel;
    }

    public function createLevel(){
        try{
            $this->checkExitLevelname();
            $levelM=$this->LModel;
            parent::__construct();
            $sql="insert into level(name,created_date,updated_date) values(?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sss',$levelM->name,$levelM->createdate,$levelM->updatedate);
            if( $stmt->execute()){
                PrintJSON([],"Create Level Name: $levelM->name Success Fully!",1);
                $this->closeall($stmt);
            }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
        

    }
    public function updateLevel(){
        try{
            $this->checkExitLevelname();
            $this->checkExitLevelId();
            $levelM=$this->LModel;
            $createD=$this->getDateCreate();

            parent::__construct();
            $sql="update level set name=?, created_date=?, updated_date=? where id=?";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssss',$levelM->name,$createD,$levelM->updatedate, $levelM->id);
                if( $stmt->execute()){
                    PrintJSON([],"update Level Name: $levelM->name Success Fully!",1);
                    $this->closeall($stmt);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }

    }

    public function viewLevel(){
        $this->checkExitLevelId();
        $levelM=$this->LModel;
        try{  
            parent::__construct();
            $stmt = $this->prepare("select * from level where id=?");  
            $stmt->bind_param('s', $levelM->id);
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
                    PrintJSON([],"Level Id: $levelM->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function deleteLevel(){
        try{
            $this->checkExitLevelId();
            $levelM=$this->LModel;
            parent::__construct();
            $sql="delete from level where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('s', $levelM->id);
            if($stmt->execute()){
                PrintJSON([],"Delete Level ID: $levelM->id Success Full!",1);
            }
            $this->closeall($stmt);
        }catch(Exception $e){
            print_r($e->getMessage()); 
        }
    }

    function checkExitLevelId(){
        parent::__construct();
        $sql="select id from level where id='".$this->LModel->id."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            $id=$this->LModel->id;
            PrintJSON([],"Your Level ID: $id It is not Valiable!", 0);
            die();
        }
    }

    function checkExitLevelname(){
        parent::__construct();
        $sql="select name from level where name='".$this->LModel->name."'";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $rs = $stmt->get_result(); // get the mysqli result
        if(!empty($rs->num_rows)){
            $name=$this->LModel->name;
            PrintJSON([],"your Level Name: $name already to create before", 0);
            die();
        }
    }

    public function getDateCreate(){
        parent::__construct(); 
        $stmt = $this->prepare("select created_date from level where id=?");  
        $stmt->bind_param('s', $this->LModel->id);
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