<?php
include_once ("../controllers/MainController.php");

class Discount
{

    public $id,$id_product,$name,$discount,$start_date,$end_date,$last_updated,$created_at;

    public function __construct($id_product, $name, $discount, $start_date, $end_date, $created_at)
    {
        $this->id_product = $id_product;
        $this->name = $name;
        $this->discount = $discount;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->last_updated = getCurrentDateTime();
        $this->created_at = $created_at;
       if($created_at==null){
           $this->created_at = getCurrentDateTime();
       }
    }

    public function getInsertValues(){
        return array(
            $this->id_product,
            $this->name,
            $this->discount,
            $this->start_date->format('Y-m-d H:i:s'),
            $this->end_date->format('Y-m-d H:i:s'),
            $this->last_updated,
            $this->created_at
        );
    }


}