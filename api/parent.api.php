<?php 
include_once ('../controllers/parent.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $PC=new ParentController($json); 
        $PC->createParent();    
    }else if($m==="update"){  
        $PC=new ParentController($json); 
        $PC->updateParent();   
    }else if($m==="delete"){ 
        $PC=new ParentController($json); 
        $PC->deleteParent();   
    }else if($m==="view"){  
        $PC=new ParentController($json); 
        $PC->viewParent();   
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>