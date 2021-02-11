<?php 
include_once ('../controllers/district.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $dtc=new DistrictController($json); 
        $dtc->createDistrict();    
    }else if($m==="update"){  
        $dtc=new DistrictController($json); 
        $dtc->updateDistrict();   
    }else if($m==="delete"){ 
        $dtc=new DistrictController($json); 
        $dtc->deleteDistrict();  
    }else if($m==="view"){  
        $dtc=new DistrictController($json); 
        $dtc->viewDistrict();    
    }else if($m==="viewall"){  
        $dtc=new DistrictController($json); 
        $dtc->viewAllDistrict();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>