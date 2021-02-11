<?php 
include_once ('../controllers/level.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){
        CheckAuthorize(['teacher']);
        $LVC=new LevelController($json); 
        $LVC->CreateLevel();    
    }else if($m==="update"){  
        $LVC=new LevelController($json); 
        $LVC->updateLevel();   
    }else if($m==="delete"){ 
        $LVC=new LevelController($json); 
        $LVC->deleteLevel();  
    }else if($m==="view"){  
        $LVC=new LevelController($json); 
        $LVC->viewLevel();    
    }else if($m==="viewall"){  
        $LVC=new LevelController($json); 
        $LVC->viewAllLevel();    
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>