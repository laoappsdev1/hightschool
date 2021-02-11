<?php 
include_once ('../controllers/province.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $LVC=new ProvinceController($json); 
        $LVC->createProvince();    
    }else if($m==="update"){  
        $LVC=new ProvinceController($json); 
        $LVC->updateProvince();   
    }else if($m==="delete"){ 
        $LVC=new ProvinceController($json); 
        $LVC->deleteProvince();  
    }else if($m==="view"){  
        $LVC=new ProvinceController($json); 
        $LVC->viewProvince();    
    }else if($m==="viewall"){  
        $LVC=new ProvinceController($json); 
        $LVC->viewAllProvince();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>