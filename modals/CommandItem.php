<?php
include_once ('../controllers/MainController.php');
include_once ('./Product.php');

class CommandItem
{
    public $id,$command_id,$product_name,$product_desc,$units,$product_iva,$price_iva_unit,$total_iva_price,$discount,$created_at;


    public function __construct($command_id,$units, $discount, $created_at,$product)
    {
        $this->command_id = $command_id;
        $this->units = $units;
        $this->discount = ($discount!=null)? $discount:0;
        $this->product_name = $product->name;
        $this->product_desc = $product->description;
        $this->product_iva = $product->iva;
        $this->price_iva_unit = calculateNewPrice($product->price_iva,$this->discount);
        $this->total_iva_price = ($this->price_iva_unit*$units);
        $this->created_at = ($created_at!=null)? $created_at:getCurrentDateTime();
    }


    function getInsertValues(){
        array($this->command_id,$this->units,$this->discount,$this->category_id,$this->category_name,$this->category_desc,$this->product_id,$this->product_name,$this->product_desc,$this->product_iva,$this->price_iva_unit,$this->total_iva_price,$this->created_at);
    }


}