<?php 
    include_once("../controllers/login.controller.php"); 
    try{ 
        Initialization();
        $m=isset($_SESSION['m'])?$_SESSION['m']:'';
        $json = json_decode(file_get_contents('php://input'), true);  
        if($m==='login'){ 
            $lc = new LoginController($json);
            $lc->Login(); 
        }else if($m==='logout'){ 
            echo "Logout";
        }else{
            echo "Faild";
        }
    }catch(Exception $e){
        print_r($e);
    }
?>