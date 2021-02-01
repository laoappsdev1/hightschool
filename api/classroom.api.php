<?php 
include_once ('../controllers/classroom.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $LVC=new ClassRoomController($json); 
        $LVC->createClassroom();    
    }else if($m==="update"){  
        $LVC=new ClassRoomController($json); 
        $LVC->updateClassroom();   
    }else if($m==="delete"){ 
        $LVC=new ClassRoomController($json); 
        $LVC->deleteClassroom();  
    }else if($m==="view"){  
        $LVC=new ClassRoomController($json); 
        $LVC->viewClassroom();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>