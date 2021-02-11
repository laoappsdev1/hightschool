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
            $model=$this->crModel;
            parent::__construct();
            // $stmt = $this->prepare("select * from classroom where id=?");  
            $stmt = $this->prepare("
                select 
                c.id as classroom_id,  
                c.class_number as class_number,
                l.id as level_id,
                l.name as level,
                c.created_date as created_date,
                c.updated_date as updated_date
                from classroom as c join level as l 
                on c.level_id=l.id where c.id=?"
            );  
            $stmt->bind_param('s', $model->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $arr = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $arr = $v;
                    }
                    $data=json_encode($arr);
                    $json = "{\"Data\":$data, \"Message\": \"View Classromm ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json;
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"class room Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewAllClassroom(){
        try{     
            $model=$this->crModel;
            $keywords='';
            $search=trim($model->keyword);
            if(!empty(strlen($search))){  //trim() ແມ່ນ function ຕັດ space ທັງຫນ້າ ທັງຫຼັງ
                $keywords.="and 
                (
                    c.class_number like '%".$search."%'
                    or l.name like '%".$search."%'
                )";
            }
            parent::__construct(); 
            $stmt = $this->prepare("
                select 
                c.id as classroom_id,  
                c.class_number as class_number,
                l.id as level_id,
                l.name as level,
                c.created_date as created_date,
                c.updated_date as updated_date
                from classroom as c join level as l 
                on c.level_id=l.id 
                where c.id>0 
                $keywords"
            );   
            $stmt->execute();  
            $result = $stmt->get_result();
            if(!empty($result->num_rows)){
            $arr=array();
                while($row =$result->fetch_assoc()){ 
                $arr[]=$row;
                }   
                $data= json_encode($arr);
                $json = "{\"Data\":$data, \"Message\": \"View All Classroom Success Full\", \"Status\": \"1\"}";
                echo $json;
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
        $createD=0;
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;   
    }
}

?>