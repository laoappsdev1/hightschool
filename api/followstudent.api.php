<?php 
include_once ('../controllers/followstudent.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){ 
        CheckAuthorize(['teacher']);
        $flstudent=new FollowStudentController($json);  
        $flstudent->createFollowStudent();    
    }else if($m==="update"){ 
        $flstudent=new FollowStudentController($json); 
        $flstudent->updateFollowStudent();  
    }else if($m==="delete"){
        $flstudent=new FollowStudentController($json); 
        $flstudent->deleteFollowStudent(); 
    }else if($m==="view"){ 
        $flstudent=new FollowStudentController($json); 
        $flstudent->viewFollowStudent(); 
    }else if($m==="viewall"){ 
        $flstudent=new FollowStudentController($json); 
        $flstudent->viewAllFollowStudent(); 
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>