<?php
include_once('../modals/Database.php');
include_once('../modals/Product.php');
include_once('../modals/Cart.php');
include_once('../modals/CartItem.php');
include_once('../modals/Command.php');
include_once('../modals/CommandItem.php');
include_once('./MainController.php');


if (isset($_POST['cart_id_newCommand'])) {
    $database = new Database();
    $items = $database->executeQuery('SELECT shop.cartItems.*,shop.products.name as "product_name",shop.products.description as "product_description",
shop.products.units as "product_units",shop.products.price_iva as "product_price",shop.products.iva as "product_iva",
shop.products.category_id as "product_category",
shop.discounts.discount FROM shop.cartItems
INNER JOIN shop.products ON shop.cartItems.product_id = shop.products.id 
LEFT JOIN shop.discounts ON shop.cartItems.product_id = shop.discounts.id_product 
							AND shop.discounts.discount = (SELECT max(discount) from shop.discounts WHERE start_date<= now() 
															AND end_date>= now() AND product_id=shop.cartItems.product_id)
WHERE cart_id = ?', array($_POST['cart_id_newCommand']));
    $database->closeConnection();
    $command = new Command($_POST['cart_id_newCommand'],$_SESSION["user_id"],0,null);
    $command->insertCommandInDatabase();
    $command->createCommandItems($items);
    $command->insertItemsInDatabase();
    $command->deleteCartItemsDatabase();
}