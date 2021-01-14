<?php
include_once ('../controllers/MainController.php');
include_once ('../modals/CommandItem.php');
include_once ('../modals/Database.php');
class Command
{
    public $id,$cart_id,$user_id,$status,$items,$created_at;

    public function __construct($cart_id,$user_id, $status,$created_at)
    {
        $this->cart_id = $cart_id;
        $this->user_id = $user_id;
        $this->status = ($status!=null) ? $status:0;
        $this->created_at = ($created_at!=null) ? $created_at:getCurrentDateTime();
    }

    function createCommandItems($items){
        $this->items = [];
        foreach ($items as $item){
            $product = new Product(1,$item["product_name"],$item["product_description"],$item['product_units'],1,$item["product_price"],$item['product_iva'],$item['product_category'],0);
            $commandItem = new CommandItem($this->id,$item['units'],$item['discount'],null,$product);
            array_push($this->items,$commandItem);
        }
    }

    function insertCommandInDatabase(){
        $database = new Database();
        $database->executeQuery("INSERT INTO commands (cart_id,user_id,status,created_at) VALUES (?,?,?,?)",array($this->cart_id,$this->user_id,$this->status,$this->created_at));
        $this->id = $database->executeQuery("SELECT id FROM commands WHERE cart_id=? AND user_id=? AND status=? ORDER BY id DESC",array($this->cart_id,$this->user_id,$this->status,$this->created_at))[0]['id'];
        $database->closeConnection()
;    }

    function insertItemsInDatabase(){
        $database = new Database();
        foreach ($this->items as $item){
            $database->executeQuery("INSERT INTO commandItems (command_id,units,discount,category_id,category_name,category_desc,product_id,product_name,product_desc,product_iva,price_iva_unit,total_iva_price,created) VALUES (?,?,?,?,?,?,?,?,?,?)",$item->getInsertValues());
        }
        $database->closeConnection();
    }

    function deleteCartItemsDatabase(){
        $database = new Database();
        $database->executeQuery("DELETE FROM cartItems WHERE cart_id=?",array($this->id));
    }

}