<?php 
include_once ('../controllers/student.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $STD=new StudentController($json); 
        $STD->createStudent();    
    }else if($m==="update"){ 
        $STD=new StudentController($json); 
        $STD->updateStudent();  
    }else if($m==="delete"){
        $STD=new StudentController($json); 
        $STD->deleteStudent(); 
    }else if($m==="view"){ 
        $STD=new StudentController($json); 
        $STD->viewStudent(); 
    }else if($m==="viewall"){ 
        $STD=new StudentController($json); 
        $STD->viewAllStudent(); 
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>