<?php  

    include_once("../controllers/user.controller.php"); 
   
    try{ 
        Initialization();
        $m=isset($_SESSION['m'])?$_SESSION['m']:'';
        $json = json_decode(file_get_contents('php://input'), true);   
        if($m==='create'){  
            // CheckAuthorize(['teacher']);
            $uc=new UserController($json);
            $uc->CreateUser(); 
        }else if($m==='update'){  
            // CheckAuthorize(['teacher']);
            $uc=new UserController($json);
            $uc->UpdateUser();             
        }else if($m==='view'){ 
            // CheckAuthorize(['teacher']);
            $uc=new UserController($json);
            $uc->ViewUser();
        }else if($m==='viewall'){ 
            // CheckAuthorize(['teacher']);
            $uc=new UserController($json);
            $uc->ViewAllUser();
        }else if($m==='delete'){ 
            // CheckAuthorize(['teacher']);
            $uc=new UserController($json);
            $uc->Delete();
            echo "Delete";
        }else{
            echo "Faild";
        }
    }catch(Exception $e){
        print_r($e);
    }
?>