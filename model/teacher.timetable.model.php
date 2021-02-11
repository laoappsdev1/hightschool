<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once('base.model.php');
class TeacherTimetableModel extends BASEMODEL{
    public int $id; 
    public int $teacherid;      
    public int $classroomid;     
    public int $subjectid; 
    public string $date;   
    public string $timestart; 
    public string $timeend;
    public string $status;
    public string $exam;
    public string $description;  // null

    public function __construct()
    { 
        parent::__construct();
      
    }
    
    public function ValidateAll():array{ 
        $result =array();
        foreach($this as $property=>$value){
            $c =[];
            $c = $this->validate($property);
            sizeof($c)>0?$result[]=$c:'';  
        }
        return $result;
    }

    public function validate($propertys):array{
        switch($propertys){
            case "timestart":
            return $this->validateTimestart();
            case "timeend":
            return $this->validateTimeend();
            case "date":
            return $this->validateDates();
            case "status":
            return $this->validateStatus(); 
            case "exam":
            return $this->validateExam(); 
        }
        return [];
    }
 

    function validateTimestart():array{
        $result=array();
        if ($this->timestart === "") { 
            $result[]= '{"message":"your first name is empty","status":0}';
        }
        if (!$this->validateTime($this->timestart)) { 
            $result[]= '{"message":"Teacher Time start is not format Time H:i:s","status":0}';
         }  
        return $result;
    }

    function validateTimeend():array{
        $result=array();
        if ($this->timeend === "") { 
            $result[]= '{"message":"your last name is empty","status":0}';
        }
        if (!$this->validateTime($this->timeend)) { 
            $result[]= '{"message":"Teacher Time End is not format Time H:i:s","status":0}';
         } 
        return $result;
    }  

    function validateDates():array{
        $result=array();
        if ($this->date === "") { 
            $result[]= '{"message":"Teacher Date is empty","status":0}';
        }
        if (!$this->validateDate($this->date)) { 
           $result[]= '{"message":"Teacher Date is not format date Y-m-d","status":0}';
        } 
        return $result;
    }

    function validateStatus():array{
        $result =array();
        if ($this->status === "") { 
            $result[]= '{"message":"Teacher status is empty","status":0}';
        }
        if(!in_array($this->status, CheckfollowTeacher)){
            $result[]= '{"message":"Teacher status is not valiable","status":0}';
        }    
        return $result;
    }

    function validateExam():array{
        $result =array();
        if ($this->exam === "") { 
            $result[]= '{"message":"Teacher Exam is empty","status":0}';
        }
        if(!in_array($this->exam, Examstatus)){
            $result[]= '{"message":"Teacher Exam Id is not valiable","status":0}';
        }    
        return $result;
    }
 

    function validateId():array{
        $result =array(); 
        if(isset($this->id)){
            if($this->id==''|| !is_numeric($this->id)){
                $result [] ='ID is not a number or it\'s empty';
            }
        }
        return $result;
    }

}
