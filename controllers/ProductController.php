<?php
include_once('../modals/Database.php');
include_once ('../controllers/AdminTokenController.php');
include_once ("../modals/Product.php");
include_once ("./MainController.php");
date_default_timezone_set('Europe/Madrid');
session_start();

if(isset($_POST["image_newProduct"])){
    unset($_SESSION["images_newProduct"]);
    $_POST["image_newProduct"] = json_decode($_POST["image_newProduct"]);
    if(!isset($_SESSION["images_newProduct"])){
        $_SESSION["images_newProduct"] = array();
    }
    $var = array("id_temp"=> count($_SESSION["images_newProduct"])+1,"file"=>$_POST["image_newProduct"]);
    array_push($_SESSION["images_newProduct"],$var);
    echo json_encode($var);
}


if(isset($_POST["name_newProduct"])){
    unset($_SESSION["images_newProduct"]);
    unset($_SESSION["errors_newProduct"]);
    unset($_SESSION["name_newProduct"]);
    unset($_SESSION["description_newProduct"]);
    unset($_SESSION["units_newProduct"]);
    unset($_SESSION["price_newProduct"]);
    $activated = 1;
    if(!isset($_POST["activated_newProduct"])){
        $activated = 0;
    }
    $vars = array(
        "data" => array($_POST["name_newProduct"], $_POST["description_newProduct"], $_POST["units_newProduct"], $_POST["price_newProduct"]),
        "names" => array("name_newProduct", "description_newProduct", "units_newProduct", "price_newProduct")
    );
    $errors = checkPostRequest($vars);
    if(count($errors)==0){
        $product = new Product($activated,$_POST["name_newProduct"],$_POST["description_newProduct"],abs(intval($_POST["units_newProduct"])),$_POST["priceIva_type_newProduct"],abs($_POST["price_newProduct"]),$_POST["iva_newProduct"],$_POST["category_newProduct"]);
        $database = new Database();
        $database->executeQuery("INSERT INTO products (activated,name,description,units,category_id,iva,price_iva,price_no_iva,created_at,last_modified) VALUES(?,?,?,?,?,?,?,?,?,?)",$product->getDatabaseValues());
        $database->closeConnection();
        $_SESSION["message"] = "El producte<strong> ".$_POST["name_newProduct"]." </strong> s'ha creat correctament";
        header("location: ../pages/admin_view/list-products.php");
    } else{
        $_SESSION["errors_newProduct"] = $errors;
        $_SESSION["name_newProduct"] = $_POST["name_newProduct"];
        $_SESSION["description_newProduct"] = $_POST["description_newProduct"];
        $_SESSION["units_newProduct"] = $_POST["units_newProduct"];
        $_SESSION["price_newProduct"] = $_POST["price_newProduct"];
        $_SESSION["category_newProduct"] = $_POST["category_newProduct"];
        $_SESSION["priceIva_type_newProduct"] = $_POST["priceIva_type_newProduct"];
        $_SESSION["iva_newProduct"] = $_POST["iva_newProduct"];
        header("location: ../pages/admin_view/new-product.php");
    }
}

