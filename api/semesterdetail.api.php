<?php 
include_once ('../controllers/semesterdetail.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $Smtd=new SemesterDetailController($json); 
        $Smtd->createSemesterDetail();    
    }else if($m==="update"){  
        $Smtd=new SemesterDetailController($json); 
        $Smtd->updateSemesterDetail();   
    }else if($m==="delete"){ 
        $Smtd=new SemesterDetailController($json); 
        $Smtd->deleteSemesterDetail();  
    }else if($m==="view"){  
        $Smtd=new SemesterDetailController($json); 
        $Smtd->viewSemesterDetail();    
    }else if($m==="viewall"){  
        $Smtd=new SemesterDetailController($json); 
        $Smtd->viewAllSemesterDetail();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>