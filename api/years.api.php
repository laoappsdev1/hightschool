<?php 
include_once ('../controllers/years.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $LVC=new YearsController($json); 
        $LVC->createYears();    
    }else if($m==="update"){  
        $LVC=new YearsController($json); 
        $LVC->updateYears();   
    }else if($m==="delete"){ 
        $LVC=new YearsController($json); 
        $LVC->deleteYears();  
    }else if($m==="view"){  
        $LVC=new YearsController($json); 
        $LVC->viewYears();    
    }else if($m==="viewall"){  
        $LVC=new YearsController($json); 
        $LVC->viewAllYears();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>