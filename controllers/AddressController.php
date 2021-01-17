<?php
include_once ('../modals/Database.php');
include_once ('../modals/Address.php');
include_once ('./MainController.php');
session_start();
if($_POST["user_id_newAddress"]){
    $address = new Address('Nova adreça',$_POST["user_id_newAddress"],$_SESSION["user_info"][0]["name"],$_SESSION["user_info"][0]["lastnames"],$_SESSION["user_info"][0]["email"],'','','','','','','',null);
    $address->updateChanges();
    echo json_encode($address);
}

if($_GET["address_id_getAddress"]){
    $database = new Database();
    $address = $database->executeQuery("SELECT * FROM addresses WHERE id =? AND user_id =?",array($_GET["address_id_getAddress"],$_SESSION["user_id"]));
    echo json_encode($address);
}

if($_POST["id_deleteAddress"]){
    $database = new Database();
    $database->executeQuery("DELETE FROM addresses WHERE id = ? AND user_id = ?",array($_POST["id_deleteAddress"],$_SESSION["user_id"]));
    header("location: ../pages/botiga_view/profile.php");
}