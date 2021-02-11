<?php 
include_once ('../controllers/subject.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $LVC=new SubjectController($json); 
        $LVC->createSubject();    
    }else if($m==="update"){  
        $LVC=new SubjectController($json); 
        $LVC->updateSubject();   
    }else if($m==="delete"){ 
        $LVC=new SubjectController($json); 
        $LVC->deleteSubject();  
    }else if($m==="view"){  
        $LVC=new SubjectController($json); 
        $LVC->viewSubject();    
    }else if($m==="viewall"){  
        $LVC=new SubjectController($json); 
        $LVC->viewAllSubject();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>