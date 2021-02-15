<?php  
    include_once("../controllers/schools.controller.php"); 
    // echo "sdfasdfasd";exit;
    try{ 
        Initialization();
        $m=isset($_SESSION['m'])?$_SESSION['m']:'';
        $json = json_decode(file_get_contents('php://input'), true);   
        if($m==='create'){  
            // CheckAuthorize(['teacher']);
            $uc=new SchoolController($json);
            $uc->CreateUserSchool(); 
        }else if($m==='update'){  
            // CheckAuthorize(['teacher']);
            $uc=new SchoolController($json);
            $uc->UpdateUserSchool();             
        }else if($m==='view'){ 
            // CheckAuthorize(['teacher']);
            $uc=new SchoolController($json);
            $uc->ViewUserSchool();
        }else if($m==='viewall'){ 
            // CheckAuthorize(['teacher']);
            $uc=new SchoolController($json);
            $uc->ViewAllUserSchool();
        }else if($m==='delete'){ 
            // CheckAuthorize(['teacher']);
            $uc=new SchoolController($json);
            $uc->DeleteUserSchool();
            echo "Delete";
        }else{
            echo "Faild";
        }
    }catch(Exception $e){
        print_r($e);
    }
?>