<?php
include_once ('../controllers/MainController.php');

class Cart
{
    public $id,$user_id,$created_at;

    public function __construct($user_id, $created_at)
    {
       $this->user_id = $user_id;
       $this->created_at = $created_at;
       if($created_at==null){
           $this->created_at = getCurrentDateTime();
       }
    }
}