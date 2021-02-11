<?php 

include_once ('base.controller.php');
include_once ('../model/employee.model.php');
include_once ('../model/user.model.php');
class EmployeeController extends BASECONTROLLER{ 

    public $userModel; 
    public $employeeModel; 
    public $userId; 

    public function __construct($object){
        parent::__construct();
        
        // user
        $uModel=new UserModel();  
        $uModel->parseObject($object);
        $v =[];
        $v = $uModel->ValidateAll();

        $v =array_merge($uModel->validateId(),$v);
        if(sizeof($v)>0){
            echo json_encode($v);
            die();
        }
            // employee
        $empModel=new EmployeeModel();  
        $empModel->parseObject($object);
        $v2 =[];
        $v2 = $empModel->ValidateAll();

        $v2 =array_merge($empModel->validateId(),$v2);
        if(sizeof($v2)>0){
            echo json_encode($v2);
            die();
        }

        $this->userModel = $uModel;
        $this->employeeModel = $empModel; 
        // print_r($uModel);
        // echo "<hr>";
        // print_r($empModel);
        // exit;
    }

    public function  checkExistUsername(){
        $result = [];
        $uModel=$this->userModel;
        $stmt = $this->prepare("select * from user where username=?");
        $stmt->bind_param("s", $uModel->username);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $data = [];
        while ($row = $rs->fetch_row()) {
            $result[]='user exist!';
        }
        $this->closeall($stmt);
        
        if(sizeof($result)>0){
            $m =' username %s , exist';
            $m = sprintf($m,$this->userModel->username);
            PrintJSON([],$m,0);
            die();
        }

    }
    public function craeteEmployee(){  
        try{
            $this->checkExistUsername();
            $this->CheckVillageId();
            parent::__construct();

            // create user
            $model = $this->userModel;
            $model->password=$this->getPasswordHash($model->password); 

            $sql="insert into user(username,password,token,usertype,created_date,updated_date) values(?,?,?,?,?,?)";
            $stmt = $this->prepare($sql);
            $stmt->bind_param('ssssss',$model->username,$model->password,$model->token,$model->usertype,$model->createdate,$model->updatedate);
            $stmt->execute();
            $this->userId=$stmt->insert_id;  
            $stmt->close();

            //create employee
            parent::__construct();
            $empModel = $this->employeeModel;
            $sql2="insert into employee(first_name,last_name,gender,village_id,dob,tel,remark,user_id) values(?,?,?,?,?,?,?,?)";
            $stmt2 = $this->prepare($sql2);
            $stmt2->bind_param('ssssssss',$empModel->firstname,$empModel->lastname,$empModel->gender,$empModel->villageid,$empModel->dob,$empModel->tel,$empModel->remark,$this->userId);
            if($stmt2->execute()){ 
                PrintJSON([],'Create Employee Success Full','1');
                $this->closeall($stmt2);
            }

        }catch (Exception $e){
            print_r($e->getMessage());
        }
        
    }

    function getOldpassword(){
        parent::__construct();
        $model=$this->userModel;
        $sql="select password from user";
        $stmt=$this->prepare($sql);
        $stmt->execute();
        $result=$stmt->get_result();
    foreach($result as $k=>$v){
        $pass=$v['password'];
    }
        return $pass;
    }
    function getPasswordUpdateUser(){
        $model=$this->userModel;
        if(strlen($model->password)>55){  
            $model->password=$this->getOldpassword();  
        }else{
            $model->password=$this->getPasswordHash($model->password); 
        } 
    }

    public function updateEmployee(){ 
        try{ 
            $this->CheckemployeeId();
            $this->CheckVillageId();  
            $createD=$this->getDateCreate();
                                 
            //update user  
            parent::__construct();
            $model=$this->userModel;
            $this->getPasswordUpdateUser();
            
            $sql="update user set username=?, password=?, token=?, usertype=?, created_date=?, updated_date=? where id=?";
            $stmt=$this->prepare($sql);
            $stmt->bind_param('sssssss', $model->username,$model->password,$model->token,$model->usertype,$createD,$model->updatedate,$this->employeeModel->id);
            $stmt->execute();
            $this->closeall($stmt);
            
            //update employee
            parent::__construct();
            $empModel=$this->employeeModel;
            $sql2="update employee set first_name=?, last_name=?, gender=?,village_id=?, dob=?, tel=?, remark=?, user_id=? where id=?";
            $stmt2=$this->prepare($sql2);
            $stmt2->bind_param('sssssssss', $empModel->firstname,$empModel->lastname,$empModel->gender,$empModel->villageid,$empModel->dob,$empModel->tel,$empModel->remark,$empModel->userid,$empModel->id);
            
            if($stmt2->execute()){
                PrintJSON([],"Update employee Id: $empModel->id Success Full",0);
            }

        }catch(Exception $e){
            print_r($e->getMessage());
        }
    } 

    public function deleteEmployee(){
        try{   
            $this->CheckemployeeId(); 
            $this->userId=$this->getUserId(); 

            // delete employee
            $empModel=$this->employeeModel;
            parent::__construct(); 
            $stmt = $this->prepare("delete from employee where id=?");  
            $stmt->bind_param('s', $empModel->id); 
            $stmt->execute(); 
            $this->closeall($stmt); 

            // delete user
            $uModel=$this->userModel;
            parent::__construct(); 
            $stmt2 = $this->prepare("delete from user where id=?");  
            $stmt2->bind_param('s', $this->userId); 
            // $stmt2->execute(); 
            if($stmt2->execute()){
                PrintJSON("", "Delete Employee ID: $empModel->id, and User ID: $this->userId Successfull!", 1);
            }
            $this->closeall($stmt2);  

           
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }


    public function viewEmployee(){
        try{ 
            $this->CheckemployeeId();
            $model=$this->employeeModel;
            parent::__construct();
            $stmt = $this->prepare("
                select
                e.id employee_id,
                u.id user_id,
                u.username username,
                u.password password,
                e.first_name firstname,
                e.last_name lastname, 
                e.dob dob, 
                e.tel tel, 
                e.gender gender, 
                e.remark remark,
                v.id village_id,
                v.name village,
                d.id district_id,
                d.name district,
                p.id province_id,
                p.name province
                from user as u join employee as e
                on e.user_id=u.id join village as v 
                on e.village_id=v.id  join district as d
                on v.district_id=d.id  join province as p
                on d.province_id=p.id
                where e.id=?
            ");  
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
                    $json = "{\"Data\":$data, \"Message\": \"View Employee ID: $model->id Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"User Id: $model->id Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }
    public function viewAllEmployee(){
        try{  
            $model=$this->employeeModel;
            parent::__construct();
            // $keywords='';
            // $search=trim($model->keyword);
            //     if(!empty(strlen($search))){  //trim() ແມ່ນ function ຕັດ space ທັງຫນ້າ ທັງຫຼັງ
            //         $keywords.="and 
            //         (
            //             v.name like '%".$search."%'
            //             or d.name like '%".$search."%'
            //             or p.name like '%".$search."%'
            //             or e.first_name like '%".$search."%'
            //             or e.last_name like '%".$search."%'
            //             or e.gender like '%".$search."%'
            //             or e.dob like '%".$search."%'
            //             or e.dob like '%".$search."%'
            //             or e.dob like '%".$search."%'
            //         )";
            //     }

            $stmt = $this->prepare("
            select
                e.id employee_id,
                u.id user_id,
                u.username username,
                u.password password,
                e.first_name firstname,
                e.last_name lastname, 
                e.dob dob, 
                e.tel tel, 
                e.gender gender, 
                e.remark remark,
                v.id village_id,
                v.name village,
                d.id district_id,
                d.name district,
                p.id province_id,
                p.name province
                from user as u join employee as e
                on e.user_id=u.id join village as v 
                on e.village_id=v.id  join district as d
                on v.district_id=d.id  join province as p
                on d.province_id=p.id
                where e.id>0 
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
                    $json = "{\"Data\":$data, \"Message\": \"View Employee Success Full\", \"Status\":\"1\"}";
                    echo $json; 
                    $this->closeall($stmt);
                    die;
                }else{
                    PrintJSON([],"Employee Can't valiable", 0);
                }
        }catch(Exception $e){
            print_r($e->getMessage());
        }
    }

    public function getUserId(){
        parent::__construct();
        $model =$this->employeeModel; 
        $stmt = $this->prepare("select * from employee where id=?");  
        $stmt->bind_param('s', $model->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Employee ID: $model->id, Or User ID: $model->userid is not available!", 0);
            die(); 
        }else{

            foreach($rs as $k=>$v){
                $userid=$v['user_id'];
            }
          return $userid; 
        }
        $this->closeall($stmt); 
    }

    public function getDateCreate(){
        parent::__construct();
        $Emodel =$this->employeeModel; 
        $stmt = $this->prepare("select * from user where id=?");  
        $stmt->bind_param('s', $Emodel->userid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        $createD=0;
        foreach($rs as $k=>$v){
            $createD=$v['created_date']; 
        }
        return $createD;  
    }

    public function CheckuserId(){ 
        parent::__construct(); 
        $uModel =$this->userModel; 
        $stmt = $this->prepare("select * from user where id=?");  
        $stmt->bind_param('s', $uModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "user ID: $uModel->id is not available!", 0);
            die(); 
        }
        $this->closeall($stmt); 
    }
    public function CheckVillageId(){ 
        parent::__construct(); 
        $empModel =$this->employeeModel; 
        $stmt = $this->prepare("select * from village where id=?");  
        $stmt->bind_param('s', $empModel->villageid);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Village ID: $empModel->villageid is not In Database!", 0);
            die(); 
        }
        $this->closeall($stmt); 
    }

    public function CheckemployeeId(){  
        parent::__construct();
        $empModel =$this->employeeModel;  
        if(isset($empModel->userid)){ // update employee
            $stmt = $this->prepare("select * from employee where id=? and user_id=?");  
            $stmt->bind_param('ss', $empModel->id, $empModel->userid);
            $stmt->execute();   
            $rs = $stmt->get_result(); // get the mysqli result
            if(empty($rs->num_rows)){ 
                PrintJSON("", "Employee ID: $empModel->id Or User ID: $empModel->userid, is not available!", 0);
                die(); 
            }
            $this->closeall($stmt);     
        }else{  // delete employee
        $stmt = $this->prepare("select * from employee where id=?");  
        $stmt->bind_param('s', $empModel->id);
        $stmt->execute();   
        $rs = $stmt->get_result(); // get the mysqli result
        if(empty($rs->num_rows)){
            PrintJSON("", "Employee ID: $empModel->id, is not available!", 0);
            die(); 
        }
        $this->closeall($stmt); 
        }
    }

}

?>