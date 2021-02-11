<?php 
include_once ('../controllers/score.controller.php');
try{
    Initialization();
    $m=isset($_SESSION['m'])?$_SESSION['m']:'';
    $json = json_decode(file_get_contents('php://input'), true);  
    if($m==="create"){ 
        CheckAuthorize(['teacher']);
        $score=new ScoreController($json);  
        $score->createScore();    
    }else if($m==="update"){ 
        $score=new ScoreController($json); 
        $score->updateScore();  
    }else if($m==="delete"){
        $score=new ScoreController($json); 
        $score->deleteScore(); 
    }else if($m==="view"){ 
        $score=new ScoreController($json); 
        $score->viewScore(); 
    }else if($m==="viewall"){ 
        $score=new ScoreController($json); 
        $score->viewAllScore(); 
    }else{
        echo "Data m Is not Valiable!";
    }
}catch(Exception $e){
    print_r($e->getMessage());
}

?>