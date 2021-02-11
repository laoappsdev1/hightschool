<?php 

// include_once("../controllers/user.controller.php"); 
include_once ('../controllers/employee.controller.php');
try{ 
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==='create'){ 
        CheckAuthorize(['teacher']);
        $emC=new EmployeeController($json); 
        $emC->craeteEmployee();         
    }else if($m==='update'){ 
        CheckAuthorize(['teacher']);
        $emC=new EmployeeController($json); 
        $emC->updateEmployee();  
    }else if($m==="delete"){ 
        CheckAuthorize(['teacher']);
        $emC=new EmployeeController($json); 
        $emC->deleteEmployee();  
    }else if($m==="view"){ 
        CheckAuthorize(['teacher']);
        $emC=new EmployeeController($json); 
        $emC->viewEmployee();  
    }else if($m==="viewall"){ 
        CheckAuthorize(['teacher']);
        $emC=new EmployeeController($json); 
        $emC->viewAllEmployee();  
    }else{
        echo "Data m Valiable!";
    }
}catch(Exception $e){
    print_r($e);
}

?>