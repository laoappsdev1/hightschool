<?php 
    include_once("../controllers/login.controller.php"); 
    try{ 
        Initialization();
        $m=isset($_SESSION['m'])?$_SESSION['m']:'';
        $token=isset($_SESSION['token'])?$_SESSION['token']:'';
        $json = json_decode(file_get_contents('php://input'), true);  
        if($m==='login'){ 

            // $a=array("AA","BB","CC");
            // $b=array("DD","BB");
            // // if(array_intersect($b, $a)){
            // if(array_intersect($b, $a)){
            //     echo "ok";
            // }else{
            //     echo "no";
            // } 
            // exit;
            CheckAuthorize([UserRoles::teacher]);
            // echo "nodddddddddddd";
            // exit;
            $login=new LoginController($json); 
            $login->login(); 

        // CheckAuthorize(['teacher']);
        // echo "ok";

            // print_r($json);
            // echo $json['username'];
            // checktoken
            // authorize
            // print_r(usertype);
            // echo "<hr>";
            // print_r(UserRoles::usertype); exit;
            // $pass=password_hash($json['password'], PASSWORD_BCRYPT);
            //    echo  $pass2=password_hash('User_123', PASSWORD_DEFAULT); 

            exit;
            echo $pass;
            echo " <hr>   ". strlen($pass);
            echo " <hr>   ". $pass2;
            echo " <hr>   ". strlen($pass2); 
            echo "<hr>_______________________________________";

            if(password_verify('User_1234',$pass)){
                echo "login ok";
            }else{
                echo "login failed";
            }

            exit;
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