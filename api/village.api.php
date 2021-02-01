<?php 
include_once ('../controllers/village.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $VL=new VillageController($json); 
        $VL->createVillage();    
    }else if($m==="update"){  
        $VL=new VillageController($json); 
        $VL->updateVillage();   
    }else if($m==="delete"){ 
        $VL=new VillageController($json); 
        $VL->deleteVillage();  
    }else if($m==="view"){  
        $VL=new VillageController($json); 
        $VL->viewVillage();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>