<?php
include_once ('../controllers/MainController.php');


class CartItem
{
    public $id,$product_id,$units,$cart_id,$created_at;

    public function __construct($product_id,$units,$cart_id,$created_at)
    {
        $this->product_id = $product_id;
        $this->units = $units;
        $this->cart_id = $cart_id;
        $this->created_at = $created_at;
        if($created_at==null){
            $this->created_at = getCurrentDateTime();
        }
    }


}