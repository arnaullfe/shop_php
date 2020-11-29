<?php


class ProductCategory
{
    private $id,$name,$description,$last_modified,$created_at,$activated;

    public function __construct($name, $description)
    {
        $this->name = $name;
        $this->description = $description;
        $date = new DateTime();
        $date = date_format($date, "Y-m-d H:i:s");
        $this->last_modified = $date;
        $this->created_at = $date;
        $this->activated = 1;
    }

    public function getDatabaseValues(){
        return array($this->name,$this->description,$this->last_modified,$this->created_at);
    }


}