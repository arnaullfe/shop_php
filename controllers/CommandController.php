<?php
include_once('../modals/Database.php');
include_once('../modals/Product.php');
include_once('../modals/Cart.php');
include_once('../modals/CartItem.php');
include_once('../modals/Command.php');
include_once('../modals/CommandItem.php');
include_once ('../modals/Address.php');
include_once('./MainController.php');
include_once ('./PdfController.php');
session_start();

if (isset($_POST['cart_id_newCommand'])) {
    $database = new Database();
    $items = $database->executeQuery('SELECT shop.cartItems.*,shop.products.name as "product_name",shop.products.description as "product_description",
shop.products.units as "product_units",shop.products.price_iva as "product_price",shop.products.iva as "product_iva",shop.products.id as "product_id",
shop.products.category_id as "product_category",shop.productCategory.name as "category_name",shop.productCategory.description as "category_desc",
shop.discounts.discount FROM shop.cartItems
INNER JOIN shop.products ON shop.cartItems.product_id = shop.products.id 
INNER JOIN shop.productCategory ON shop.products.category_id = shop.productCategory.id
LEFT JOIN shop.discounts ON shop.cartItems.product_id = shop.discounts.id_product 
							AND shop.discounts.discount = (SELECT max(discount) from shop.discounts WHERE start_date<= now() 
															AND end_date>= now() AND product_id=shop.cartItems.product_id)
WHERE cart_id = ?', array($_POST['cart_id_newCommand']));
    $address = $database->executeQuery("SELECT * FROM addresses WHERE id=?",array($_POST["address_id_newCommand"]))[0];
    $address_obj = new Address($address["alias"],$address["user_id"],$address["name"],$address["lastnames"],$address["email"],$address["phone"],$address["country"],$address["province"],$address["address"],$address["city"],$address["postal_code"],$address["nif"],$address["created_at"]);
    $address_obj->id =$address["id"];
    $database->executeQuery("INSERT INTO addressesCommands (nif,alias,user_id,name,lastnames,email,phone,country,province,address,city,postal_code,created_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)", $address_obj->getDataForDatabase());
    $address_command_id = $database->executeQuery("SELECT id FROM addressesCommands WHERE nif = ? AND alias = ? AND user_id=? AND name=? AND lastnames=? AND email=? AND phone=? AND country=? AND province=?AND address=? AND city=? AND postal_code=? AND created_at=? ORDER BY id DESC", $address_obj->getDataForDatabase())[0]['id'];
    $database->closeConnection();
    $command = new Command($_POST['cart_id_newCommand'],$_SESSION["user_id"],$address_command_id,0,$_POST["sending_price_newCommand"],null);
    $command->insertCommandInDatabase();
    $command->createCommandItems($items);
    $command->insertItemsInDatabase();
    $command->deleteCartItemsDatabase();
    createBill($command->id);
}