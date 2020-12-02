<?php
include_once('../modals/Database.php');
include_once ('../controllers/AdminTokenController.php');
include_once ("../modals/Product.php");

session_start();
unset($_SESSION["images_newProduct"]);
if($_POST["image_newProduct"]){
    $_POST["image_newProduct"] = json_decode($_POST["image_newProduct"]);
    if(!isset($_SESSION["images_newProduct"])){
        $_SESSION["images_newProduct"] = array();
    }
    $var = array("id_temp"=> count($_SESSION["images_newProduct"])+1,"file"=>$_POST["image_newProduct"]);
    array_push($_SESSION["images_newProduct"],$var);
    echo json_encode($var);
} else{
    echo json_encode("fora");
}


