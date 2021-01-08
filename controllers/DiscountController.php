<?php
include_once ("../modals/Discount.php");
include_once ("../modals/Database.php");
include_once ("./MainController.php");
date_default_timezone_set('Europe/Madrid');
session_start();

if(isset($_POST["product_createDiscount"])){
    $vars = array(
        "data" => array($_POST["product_createDiscount"], $_POST["name_createDiscount"], $_POST["discount_createDiscount"], $_POST["datetime_createDiscount"]),
        "names" => array("product_createDiscount", "name_createDiscount", "discount_createDiscount", "datetime_createDiscount")
    );
    $errors = checkPostRequest($vars);
    if(count($errors)>0){
        $_SESSION["error_message"] = "<strong>Error!</strong> Per poder crear el descompte introdueix tots els camps";
        header("location: ../pages/admin_view/list-discounts.php");
    }else if($_POST["discount_createDiscount"]<=100){
        $dates = rangeDateTimeToArray($_POST["datetime_createDiscount"]);
        $highlight_discount = 0;
        if(isset($_POST["highlight_createDiscount"]) && $_POST["highlight_createDiscount"]=="on"){
            $highlight_discount = 1;
        }
        $discount = new Discount($_POST["product_createDiscount"],$_POST["name_createDiscount"],$_POST["discount_createDiscount"],$dates[0],$dates[1],null,$highlight_discount);
        $database = new Database();
        $database->executeQuery("INSERT INTO discounts (id_product,name,discount,start_date,end_date,highlight,last_updated,created_at) values (?,?,?,?,?,?,?,?)",$discount->getInsertValues());
        $database->closeConnection();
        $_SESSION["message"] = "<strong>Enhorabona!</strong> Descompte creat correctament";
        header("location: ../pages/admin_view/list-discounts.php");
    } else{
        $_SESSION["error_message"] = "<strong>Error!</strong> El descompte pot ser màxim d'un 100%";
        header("location: ../pages/admin_view/list-discounts.php");
    }
}
