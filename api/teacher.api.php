<?php 
include_once ('../controllers/teacher.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $tC=new TeacherController($json); 
        $tC->createTeacher();    
    }else if($m==="update"){ 
        $tC=new TeacherController($json); 
        $tC->updateTeacher();  
    }else if($m==="delete"){
        $tC=new TeacherController($json); 
        $tC->deleteTeacher(); 
    }else if($m==="view"){ 
        $tC=new TeacherController($json); 
        $tC->viewTeacher(); 
    }else if($m==="viewall"){ 
        $tC=new TeacherController($json); 
        $tC->viewAllTeacher(); 
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>