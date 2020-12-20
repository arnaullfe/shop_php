<?php


class CartItem
{
    public $id,$id_product,$units,$id_users,$added_at;

    public function __construct($id_product, $units, $id_users, $added_at)
    {
        $this->id_product = $id_product;
        $this->units = $units;
        $this->id_users = $id_users;
        $this->added_at = $added_at;
    }


}