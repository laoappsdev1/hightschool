<?php
include_once("../service/service.php");
class Tojson{
    public function toJSON(): array
    {
        return get_object_vars($this);
    }
    
    public function exportToJSON(){
        return json_encode($this->toJSON());
    }

    public function parseObject($object){ 
        if (!$object) {
            PrintJSON(array(),"data is empty",0);
            die();
        }
        foreach ($object as $property => $value) {
            if (property_exists(get_class($this), $property)) {
                $this->$property = $value;
            }
        } 
    }

    function validateDate($date, $format = 'Y-m-d')
        {
            $d = DateTime::createFromFormat($format, $date);
            return $d && $d->format($format) == $date;
        }
}
?>