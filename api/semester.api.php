<?php 
include_once ('../controllers/semester.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $smt=new SemesterController($json); 
        $smt->createSemester();    
    }else if($m==="update"){  
        $smt=new SemesterController($json); 
        $smt->updateSemester();   
    }else if($m==="delete"){ 
        $smt=new SemesterController($json); 
        $smt->deleteSemester();  
    }else if($m==="view"){  
        $smt=new SemesterController($json); 
        $smt->viewSemester();    
    }else if($m==="viewall"){  
        $smt=new SemesterController($json); 
        $smt->viewAllSemester();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>