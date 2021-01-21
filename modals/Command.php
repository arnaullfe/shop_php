<?php
include_once ('../controllers/MainController.php');
include_once ('../modals/CommandItem.php');
include_once ('../modals/Database.php');
class Command
{
    public $id,$cart_id,$user_id,$status,$items,$address_command_id,$sending_price,$created_at;

    public function __construct($cart_id,$user_id,$address_command_id,$status,$sending_price,$created_at)
    {
        $this->cart_id = $cart_id;
        $this->user_id = $user_id;
        $this->status = ($status!=null) ? $status:0;
        $this->address_command_id = $address_command_id;
        $this->sending_price = $sending_price;
        $this->created_at = ($created_at!=null) ? $created_at:getCurrentDateTime();
    }

    function createCommandItems($items){
        $this->items = [];
        foreach ($items as $item){
            $product = new Product(1,$item["product_name"],$item["product_description"],$item['product_units'],1,$item["product_price"],$item['product_iva'],$item['product_category'],0);
            $product->id = $item["product_id"];
            $commandItem = new CommandItem($this->id,$item['units'],$item['discount'],null,$product,$item["category_name"],$item["category_desc"]);
            array_push($this->items,$commandItem);
        }
    }

    function insertCommandInDatabase(){
        $database = new Database();
        $database->executeQuery("INSERT INTO commands (cart_id,user_id,status,address_command_id,sending_price,created_at) VALUES (?,?,?,?,?,?)",array($this->cart_id,$this->user_id,$this->status,$this->address_command_id,$this->sending_price,$this->created_at));
        $this->id = $database->executeQuery("SELECT id FROM commands WHERE cart_id=? AND user_id=? AND status=? AND address_command_id=? AND created_at=? ORDER BY id DESC",array($this->cart_id,$this->user_id,$this->status,$this->address_command_id,$this->created_at))[0]['id'];
        $database->closeConnection();
    }

    function insertItemsInDatabase(){
        $database = new Database();
        foreach ($this->items as $item){
            $database->executeQuery("UPDATE product SET units= units - ? WHERE id=?",array($item->units,$item->product_id));
            $database->executeQuery("INSERT INTO commandItems (command_id,units,discount,category_id,category_name,category_desc,product_id,product_name,product_desc,product_iva,price_iva_unit,total_iva_price,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)",$item->getInsertValues());

        }
        $database->closeConnection();
    }

    function deleteCartItemsDatabase(){
        $database = new Database();
        $database->executeQuery("DELETE FROM cartItems WHERE cart_id=?",array($this->cart_id));
    }

}