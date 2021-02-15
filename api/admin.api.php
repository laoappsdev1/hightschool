<?php  
$email_a = 'joe@example.com'; 

if (!filter_var($email_a, FILTER_VALIDATE_EMAIL)) {
    echo "Email address '$email_a' is considered valid.\n";
}else{
    
}


exit;

    include_once("../controllers/admin.controller.php"); 
    // echo "sdfasdfasd";exit;
    try{ 
        Initialization();
        $m=isset($_SESSION['m'])?$_SESSION['m']:'';
        $json = json_decode(file_get_contents('php://input'), true);   
        if($m==='create'){  
            // CheckAuthorize(['teacher']);
            $uc=new AdminController($json);
            $uc->CreateUserAdmin(); 
        }else if($m==='update'){  
            // CheckAuthorize(['teacher']);
            $uc=new AdminController($json);
            $uc->UpdateUserAdmin();             
        }else if($m==='view'){ 
            // CheckAuthorize(['teacher']);
            $uc=new AdminController($json);
            $uc->ViewUserAdmin();
        }else if($m==='viewall'){ 
            // CheckAuthorize(['teacher']);
            $uc=new AdminController($json);
            $uc->ViewAllUserAdmin();
        }else if($m==='delete'){ 
            // CheckAuthorize(['teacher']);
            $uc=new AdminController($json);
            $uc->DeleteUserAdmin();
            echo "Delete";
        }else{
            echo "Faild";
        }
    }catch(Exception $e){
        print_r($e);
    }
?>