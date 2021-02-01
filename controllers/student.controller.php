<?php 

include_once ('base.controller.php');
include_once ('../model/student.model.php');
include_once ('../model/parent.model.php');
class StudentController extends BASECONTROLLER{ 

    public $studentModel;  
    public $parent_id; 

    public function __construct($object){
        parent::__construct(); 

            // student
        $StdModel=new StudentModel();  
        $StdModel->parseObject($object);
        $v2 =[];
        $v2 = $StdModel->ValidateAll();

        $v2 =array_merge($StdModel->validateId(),$v2);
        if(sizeof($v2)>0){
            echo json_encode($v2);
            die();
        } 
        $this->studentModel = $StdModel;  
    }

    
    public function createStudent(){  
        try{  
            $this->checkExistParentId();
            $this->CheckVillageId();   
            //create student
            parent::__construct(); 

            if(!empty($this->studentModel->img))
            {
                $img_name="Student_".time().rand(100,999).".".getbase64_name($this->studentModel->img); 
                base64_to_jpeg($this->studentModel->img, dir_images.$img_name);    
                $this->studentModel->img=$img_name;
            } 
            $STD = $this->studentModel;  
            $sql="insert into student(firstname,lastname,gender,village_id,dob,tel,image,fromschool,parent_id,created_date,updated_date) values(?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('sssssssssss',$STD->firstname,$STD->lastname,$STD->gender,$STD->villageid,$STD->dob,$STD->tel,$STD->img,$STD->fromschool,$STD->parentid,$STD->createdate,$STD->updatedate);
            if($stmt->execute()){ 
                PrintJSON([],"Create Student First Name: $STD->firstname, Success Full",0);
                $this->closeall($stmt);
            } 
        }catch (Exception $e){
            print_r($e->getMessage());
        }
        
    }
    public function updatestudent(){ 
        try{ 
            $this->CheckstudentId();
            $this->CheckVillageId();
            $this->checkExistParentId(); 
            //update student
            parent::__construct();
             
            if(!empty($this->studentModel->img))
            { 
                $this->DeleteOldFile(); 
                $Img_name="Student".time().rand(100,999).".".getbase64_name($this->studentModel->img); 
                base64_to_jpeg($this->studentModel->img, dir_images.$Img_name);   
                $this->studentModel->img=$Img_name;
            }else{
                $this->studentModel->img=$this->getOldFile(); 
            }
            $createD=$this->getDateCreate(); 
            $STD=$this->studentModel;
            $sql2="update student set firstname=?, lastname=?, gender=?,village_id=?, dob=?, tel=?, parent_id=? , fromschool=? , image=?, created_date=?, updated_date=?  where id=?";
            $stmt2=$this->prepare($sql2);
            $stmt2->bind_param('ssssssssssss', $STD->firstname,$STD->lastname,$STD->gender,$STD->villageid,$STD->dob,$STD->tel,$STD->parentid,$STD->fromschool,$STD->img,$createD,$STD->updatedate,$STD->id);
            if($stmt2->execute()){ 
                PrintJSON([],"Update Student Firstname: $STD->firstname Success Full",1);
            }

        }catch(Exception $e){
            print_r($e->getMessage());
        }
    } 

    public function deletestudent(){
        try{   
            $this->CheckstudentId();  
            $this->DeleteOldFile(); 
            // delete student
            $STD=$this->studentModel;
            parent::__construct(); 
            $stmt = $this->prepare("delete from student where id=?");  
            $stmt->bind_param('s', $STD->id); 
            $stmt->execute();  
            if($stmt->execute()){
                PrintJSON([], "Delete Student ID: $STD->id, Successfull!", 1);
            }
            $this->closeall($stmt);             
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function viewstudent(){
        try{ 
            $this->CheckstudentId();
            $STD=$this->studentModel;
            parent::__construct();
            $stmt = $this->prepare("select * from student where id=?");  
            $stmt->bind_param('s', $STD->id);
            $stmt->execute();  
            $rs = $stmt->get_result(); 
            $studentArray = array();
            if(!empty($rs->num_rows)){
                    foreach($rs as $k=>$v)
                    {
                        $studentArray[] = $v;
                    }
                    $jsonObj='"Data":{'.json_encode($studentArray, true).',"Message":"Select Data Success Full","status":1}';
                    echo json_encode($jsonObj);
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"student Id: $STD->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function  checkExistParentId(){
        $result = [];
        parent::__construct();
        $stdM=$this->studentModel;
        $stmt = $this->prepare("select * from parent where id=?");
        $stmt->bind_param("s", $stdM->parentid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON([],"Your Parent ID:  is not Valiable!",0);
            die;
        }
    }

    

    public function getDateCreate(){
        parent::__construct();
        $STD =$this->studentModel; 
        $stmt = $this->prepare("select * from student where id=?");  
        $stmt->bind_param('s', $STD->parentid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $createD=[];
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;   
    }

    public function CheckparentId(){ 
        parent::__construct(); 
        $STD =$this->parentModel; 
        $stmt = $this->prepare("select * from parent where id=?");  
        $stmt->bind_param('s', $STD->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "parent ID: $STD->id is not available!", 0);
            die(); 
        }
        $this->closeall($stmt); 
    }
    public function CheckVillageId(){ 
        parent::__construct(); 
        $teachModel =$this->studentModel; 
        $stmt = $this->prepare("select * from village where id=?");  
        $stmt->bind_param('s', $teachModel->villageid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Village ID: $teachModel->villageid is not In Database!", 0);
            die(); 
        }
        $this->closeall($stmt); 
    }

    public function CheckstudentId(){  
        parent::__construct();
        $STD =$this->studentModel;  
        // if(isset($STD->parentid)){ // update student
        //     $stmt = $this->prepare("select * from student where id=? and parent_id=?");  
        //     $stmt->bind_param('ss', $STD->id, $STD->parentid);
        //     $stmt->execute();   
        //     $rs = $stmt->get_result(); // get the mysqli result
        //     if(empty($rs->num_rows)){ 
        //         PrintJSON("", "student ID: $STD->id Or parent ID: $STD->parentid, is not available!", 0);
        //         die(); 
        //     }
        //     $this->closeall($stmt);     
        // }else{  // delete student
        $stmt = $this->prepare("select * from student where id=?");  
        $stmt->bind_param('s', $STD->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "student ID: $STD->id, is not available!", 0);
            die(); 
        }
        $this->closeall($stmt); 
        // }
    }

    

    function DeleteOldFile(){
        parent::__construct();
        $stmt=$this->prepare("select image from student where id ={$this->studentModel->id}"); 
        $stmt->execute();
        $rs = $stmt->get_result();
        if(!empty($rs->num_rows)){
            foreach($rs as $k=>$v){
                if(!empty($v['image'])){
                    @unlink(dir_images.$v['image']); 
                }
            } 
        }else{
            echo "no data";
        }
        
    }

    function getOldFile(){
        parent::__construct();
        $stmt=$this->prepare("select image from student where id ={$this->studentModel->id}"); 
        $stmt->execute();
        $imagename='';
        $rs = $stmt->get_result();
        if(!empty($rs->num_rows)){
            foreach($rs as $k=>$v){
                if(!empty($v['image'])){ 
                    $imagename=$v['image'];
                }
            } 
        } 
        return $imagename;
        
    }

}

?>