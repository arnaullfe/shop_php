<?php
include_once ("../controllers/MainController.php");

class Tag
{
    public $id,$color,$name,$created_at;

    public function __construct($name,$color,$created_at)
    {
        $this->color = $color;
        $this->name = $name;
        $this->created_at = ($created_at!=null)? $created_at:getCurrentDateTime();
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getDatabaseValues(){
        return array($this->name,$this->color,$this->created_at);
    }


}