<?php

include_once('tojson.model.php'); 


class BASEMODEL extends Tojson{
    private ?int $id;
    public string $createdate;
    public string $updatedate;
    public function __construct()
    {
        $this->createdate=timestamp;
        $this->updatedate=timestamp;
    }
    public function getId(){
        return isset($this->id)?$this->id:'';
    }
    public function setId(int $id){
        $this->id=$id;
    }
    // function checkId(){
    //     if($this->id==""|| is_nan($this->id)){
    //         echo '{"message":"id is not valids"}';
    //         die();
    //     }
    // }
}

    // $l = new BASEMODEL();
    // $l->token = 'token';
    // $l->loginIP = '127.0.0.1';
    // $js =$l->toJSON();
    // echo json_encode($js);
    // die();
?>