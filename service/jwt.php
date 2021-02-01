<?php

use Firebase\JWT\JWT;

require_once "../vendor/firebase/php-jwt/src/BeforeValidException.php";
require_once "../vendor/firebase/php-jwt/src/ExpiredException.php";
require_once "../vendor/firebase/php-jwt/src/SignatureInvalidException.php";
require_once "../vendor/firebase/php-jwt/src/JWT.php";


// use \Firebase\JWT\JWT;
// $k=registerToken(["username"=>"touy","password"=>"OK"]);
// echo $k;
// checkToken($k);
// $k=refreshToken($k);
// echo $k;
//echo json_encode( ['status'=>'stopped']);  
// echo checkToken("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJsYW9hcHBzLmNvbSIsImF1ZCI6Imp3dC5sYW9hcHBzLmNvbSIsImlhdCI6MTM1Njk5OTUyNCwibmJmIjoxMzU3MDAwMDAwLCJkYXRhIjp7InVpZCI6MSwidW5hbWUiOiJhZG1pbmlzdHJhdG9yMSIsInBhc3MiOiIyMjIyMjIiLCJlbWFpbCI6IiIsInJ0aW1lIjoiMDU6MzMiLCJybWFpbCI6MSwibXR5cGUiOiIwXzFfMSwxXzBfMSwyXzBfMSwwXzBfMiwwXzBfMywwXzFfNCwwXzBfMTIsMF8wXzEzLDBfMV8xNCIsInZhbGlkIjoxLCJybmFtZSI6IkFkbWluaXN0cmF0b3IiLCJsYXQiOjE3OTY0NjUzLCJsbmciOjEwMjYwNzE1MSwiZGF0ZV9mbXQiOiJ5eXl5LU1NLWRkIiwidGltZV9mbXQiOiJISDptbTpzcyIsInNvbmRfYWxhcm0iOjEsInBvcHVwX2FsYXJtIjoxLCJ1ZCI6MCwidWYiOjAsInV0IjowLCJ1cyI6MH0sInVwZGF0ZXRpbWUiOjE1NzU1NTkwMzc4MzMzfQ.neqHM7XmZwXmfRZU7FT8G12YR3h9ArxWvc6r4xLbbOY");
function registerToken($user){
   // var_dump($user);
    $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
    $payload = array(
        "iss" => 'laoapps.com',
        "aud" => "jwt.laoapps.com",
        "iat" => 1356999524,
        "nbf" => 1357000000,
        "data" => $user,
        "updatetime"=>tickTime(),
        'exp' => addTime(60*60*24), // ?
        'cre' => addTime(),
       
    );
    $jwt = JWT::encode($payload, $key);

    return $jwt;
}
function GetJWTObj($jwt){
    try {
        $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $decoded_array = (array) $decoded;
        return $decoded_array ; 
    }
    catch (\Exception $e) {
        //die($e);
        return null;
    }
    // finally {
    //     //optional code that always runs
    // }
    return null;
}
function getAvailableToken($jwt){
    try {
        $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $decoded_array = (array) $decoded;
    
        $exp = $decoded_array ['exp'];
        return $exp;
    }
    catch (\Exception $e) {
        //die($e);
        return null;
    }

    return null;
}
function getDetailsToken($jwt){
    try {
        $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $decoded_array = (array) $decoded;
        $user = $decoded_array ['data'];
        return $user;
        // echo $user; exit;
        // print_r($user);
        // check user from database;
        // $details=(object)array();
        // if(isset($user)){
        //     $details->timezone =  $user->timezone;
        //     $details->client_time_zone =  $user->client_time_zone;
        //     $details->lang =  $user->lang;
        //     $details->date_fmt =  $user->date_fmt;
        //     $details->time_fmt =  $user->time_fmt;
        //     $details->sond_alarm =  $user->sond_alarm;
        //     $details->popup_alarm =  $user->popup_alarm;
        //     $details->unit_distance =  $user->ud;
        //     $details->unit_fuel =  $user->uf;
        //     $details->unit_temperature =  $user->ut;
        //     $details->unit_speed =  $user->us;
        //     $details->okind = $user->okind;
        //     $details = json_encode($details);
        //     return $details;
        // }
    }
    catch (\Exception $e) {
        //die($e);
        return null;
    }
    // finally {
    //     //optional code that always runs
    // }
    return null;
}
function authorizeToken($jwt){
    try {
        $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $decoded_array = (array) $decoded;
    
        $user = $decoded_array ['data'];
        // print_r($user);
        // check user from database;
        if(isset($user)){
            return $user->pass;
        }
    }
    catch (\Exception $e) {
        //die($e);
        return null;
    }
    // finally {
    //     //optional code that always runs
    // }

    return null;
}
function checkToken($jwt){
    try {
        $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $decoded_array = (array) $decoded;

        $user = $decoded_array ['data'];
        // print_r($user);die();
        // check user from database;
    
        if(isset($user)){
            return $user->uid;
        }
    }
    catch (\Exception $e) {
        //die($e);
        return -1;
    }
    return -1;
}
function allDetailsToken($jwt){
    $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $decoded_array = (array) $decoded;

    $user = $decoded_array ['data'];
    // print_r($user);
    // check user from database;
    if(isset($user)){        
        return $user;
    }
    return null;
}
function unitSpeedToken($jwt){
    $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $decoded_array = (array) $decoded;

    $user = $decoded_array ['data'];
    // print_r($user);
    // check user from database;
    if(isset($user)){
        return $user->us;
    }
    return 0;
}
function timeZoneToken($jwt){
    $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $decoded_array = (array) $decoded;

    $user = $decoded_array ['data'];
    // print_r($user);
    // check user from database;
    if(isset($user)){
        return $user->client_time_zone;
    }
    return 0;
}
function refreshToken($jwt){
    $key = "31ZlkkPKf2kBSARuYmwpfes6FyobGOfF";
    $decoded = JWT::decode($jwt, $key, array('HS256'));
    $decoded_array = (array) $decoded;
    return registerToken($decoded_array['data']);
}
function tickTime(){
    $mt = microtime(true);
    $mt =  $mt*1000; //microsecs
    return (string)$mt*10; //100 Nanosecs
}
?>