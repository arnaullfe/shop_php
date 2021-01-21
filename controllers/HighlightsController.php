<?php
include_once ('../modals/Database.php');
session_start();

if(isset($_POST["product_id_highlight"])){
    $activated = ($_POST["activated_highlight"]!=null && isset($_POST["activated_highlight"]) && $_POST["activated_highlight"]=="on")? 1:0;
    $database = new Database();
    $image = $database->executeQuery("SELECT url FROM shop.images_product WHERE id_product=?",array($_POST["product_id_highlight"]));
    $image = (count($image)>0)? $image[0]['url']:'';
    $database->executeQuery("UPDATE shop.highlights set product_id=?,highlight_type=?,title=?,url=? WHERE id=1",array($_POST["product_id_highlight"],$activated,$_POST["name_highlight"],$image));
    $database->closeConnection();
    header("location: ../pages/admin_view/list-highlight.php");
}