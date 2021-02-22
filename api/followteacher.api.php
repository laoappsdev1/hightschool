<?php 
include_once ('../controllers/followteacher.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){ 
        CheckAuthorize(['teacher']);
        $FLC=new FollowTeacherController($json);  
        $FLC->createFollowTeacher();    
    }else if($m==="update"){ 
        $FLC=new FollowTeacherController($json); 
        $FLC->updateFollowTeacher();  
    }else if($m==="delete"){
        $FLC=new FollowTeacherController($json); 
        $FLC->deleteFollowTeacher(); 
    }else if($m==="view"){ 
        $FLC=new FollowTeacherController($json); 
        $FLC->viewFollowTeacher(); 
    }else if($m==="viewbyteacher"){ 
        $FLC=new FollowTeacherController($json); 
        $FLC->viewByTeacher(); 
    }else if($m==="viewall"){ 
        $FLC=new FollowTeacherController($json); 
        $FLC->viewAllFollowTeacher(); 
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>