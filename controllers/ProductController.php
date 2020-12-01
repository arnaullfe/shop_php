<?php
include_once('../../modals/Database.php');
include_once ('../../controllers/AdminTokenController.php');
include_once ("../modals/Product.php");
session_start();

if(isset($_POST["category_newProduct"]) && isset($_POST["name_newProduct"]) && isset($_POST["description_newProduct"])){
 //   $product = new Product($_POST["name_newProduct"],$_POST["description_newProduct"],)
}