<?php 

// include_once("../controllers/user.controller.php"); 
include_once ('../controllers/levelstudent.controller.php');
try{ 
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==='create'){ 
        CheckAuthorize(['teacher']);
        $lvsc=new LevelStudentController($json); 
        $lvsc->createLevelStudent();         
    }else if($m==='update'){ 
        CheckAuthorize(['teacher']);
        $lvsc=new LevelStudentController($json); 
        $lvsc->updateLevelStudent();  
    }else if($m==="delete"){ 
        CheckAuthorize(['teacher']);
        $lvsc=new LevelStudentController($json); 
        $lvsc->deleteLevelStudent();  
    }else if($m==="view"){ 
        CheckAuthorize(['teacher']);
        $lvsc=new LevelStudentController($json); 
        $lvsc->viewLevelStudent();  
    }else if($m==="viewbyteacher"){ 
        CheckAuthorize(['teacher']);
        $lvsc=new LevelStudentController($json); 
        $lvsc->viewbyteacher();  
    }else if($m==="viewbylevel"){ 
        CheckAuthorize(['teacher']);
        $lvsc=new LevelStudentController($json); 
        $lvsc->viewbylevel();  
    }else if($m==="viewall"){ 
        CheckAuthorize(['teacher']);
        $lvsc=new LevelStudentController($json); 
        $lvsc->viewAllLevelStudent();  
    }else{
        echo "Data m Valiable!";
    }
}catch(Exception $e){
    print_r($e);
}

?>