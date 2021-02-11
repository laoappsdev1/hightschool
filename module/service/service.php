<?php
//session_start();

use Firebase\JWT\JWT;

// include_once('jwt.php');
error_reporting(E_ALL ^ E_NOTICE); 		
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept,token,m,Authorization");

define('timestamp',date('Y-m-d H:i:s'));
define('usertype',array('parent','teacher','employee','123456',"mmmmkkk"));
define('gender',array('male','female','other'));
define('CheckfollowTeacher',array('empty','come','notcome'));
define('checkStudentLevelStatus',array('empty','studying','pass','notpass'));
define('Examstatus',array('study','exam'));
define('semesterdetailmonth',array('9','10','11','12','1','2','3','4','5','6'));
define('dir_images','../images/');

define('TOKEN_KEY','asfdsdfsfsadfasdfysd09a7a0t012phtjipjfdjsdaf45342tdf'); 

abstract class UserRoles{
    const student="parent";
    const teacher = "teacher";
    const employee = "employee";
    const  usertype = array("parent","teacher","employee");
}

function addTime(int $second=0){ 
    return date('Y-m-d h:i:s', time()+$second);
}

function subTime(int $second=0){
    return date('Y-m-d h:i:s', time()-$second);
}


function Formatdatetime($timestamp){
    $ts = $timestamp;
    $date = new DateTime("@$ts");
    // echo $date->format('U = Y-m-d H:i:s') . "\n";
    echo $date->format('Y-m-d H:i:s');
}


function PrintJSON($data, $message, $status)
{
    $format = '{"data":"%s","message":"%s","status":"%s"}';
    if ($data) {
        if (sizeof($data) > 0) {
            printf($format, json_encode($data), $message, $status);
        } else {
            printf($format, json_encode([$data]), $message, $status);
        }

    } else {
        printf($format, "[]", $message, $status);
    }
}
function Initialization()
{
    $token = isset(getallheaders()['token'])?getallheaders()['token']:'';
    $_SESSION['m'] = isset(getallheaders()['m'])?getallheaders()['m']:'';
    $_SESSION['token']=$token;
    $_SESSION['user'] = getDetailsToken($token);
    $_SESSION['exp'] = getAvailableToken($token);
    
    // if ((isset($token) and checkToken($token))) {
    //     $tokenuid=-1;
    //     if (isset($token)) {
    //         $tokenuid = checkToken($token);
    //     }
    //     if ($tokenuid > -1) {
    //         $user_id = $tokenuid;
    //         $_SESSION["uid"] = $user_id;
    //         $_SESSION['pass'] = authorizeToken($token);
    //     } else {
    //         echo json_encode(array('status' => 0, 'message' => 'you have no Authorize'));
    //         die();
    //     }

    // } else {

    //     echo json_encode(array('status' => 0, 'message' => 'No Authorize'));
    //     die();
    // }
}
function CheckAuthorize(array $authorizes){
    // "iss" => 'laoapps.com',
    // "aud" => "jwt.laoapps.com",
    // "iat" => 1356999524,
    // "nbf" => 1357000000,
    // "data" => $user,
    // "updatetime"=>tickTime(),
    // 'exp' => addTime(60*60*24), // ?
    // 'cre' => addTime(),
//    $authorizes =UserRoles::usertype;
//    print_r($authorizes);exit;
   if(!isset($_SESSION['token'])){
    PrintJSON([],'No Authorize',0);
    die();
   }

   $obj = GetJWTObj($_SESSION['token']);
   if($obj){
        $exp = $obj['exp'];
    // check exp
        $user = $obj['data']; // id
      //select role from users where id = id;
       $rule = UserRoles::usertype;
    //    if(!in_array($authorizes,$rule)){
       if(!array_intersect($rule,$authorizes)){
        PrintJSON([],'No authorize AA',0);
        die();
       }

      //$user
   }else{
       PrintJSON([],'No Authorize',0);
       die();
   }
   
}

function GetMethod(){
    return  isset(getallheaders()['m'])?getallheaders()['m']:die(json_encode(array("status"=>"wrong method")));
}

// function IsMyself(){
//     return  isset(getallheaders()['view'])?true:false;
// }
 

function base64_to_jpeg($base64_string, $output_file) { 
    $ifp = fopen( $output_file, 'wb' );  
    $data = explode( ',', $base64_string); 
    fwrite( $ifp, base64_decode( $data[ 1 ] ) ); 
    fclose( $ifp );  
    return $output_file;  
}

function getbase64_name($base64_string) {  
    $splited = explode(',', substr($base64_string , 5 ) , 2);
    $mime=$splited[0]; 
    $mime_split_without_base64=explode(';', $mime,2);
    $mime_split=explode('/', $mime_split_without_base64[0],2);   
    return $mime_split[1];
}


// function DeleteOldFile(){
//     unlink('../images/Timage_1611822094907.jpeg'); 
// }

// function GetallFileINDir(){
//     $directory = '../images/';
//     $scanned_directory = array_diff(scandir($directory), array('..', '.'));
//     // foreach($scanned_directory as $k=>$v){
//     //     echo "<p>".$v;
//     // }
//     return $scanned_directory;
// }
