<?php 
include_once ('../controllers/teachertimetable.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $ttC=new TeacherTimetableController($json); 
        $ttC->createTeacherTimetable();    
    }else if($m==="update"){ 
        $ttC=new TeacherTimetableController($json); 
        $ttC->updateTeacherTimetable();  
    }else if($m==="delete"){
        $ttC=new TeacherTimetableController($json); 
        $ttC->deleteTeacherTimetable(); 
    }else if($m==="view"){ 
        $ttC=new TeacherTimetableController($json); 
        $ttC->viewTeacherTimetable(); 
    }else if($m==="viewforteacher"){ 
        $ttC=new TeacherTimetableController($json); 
        $ttC->viewforteacher(); 
    }else if($m==="viewall"){ 
        $ttC=new TeacherTimetableController($json); 
        $ttC->viewAllTeacherTimetable(); 
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>