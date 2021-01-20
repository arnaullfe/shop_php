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

if(isset($_POST["id_editAddress"])){
    $address = new Address($_POST["alias_editAddress"],$_SESSION["user_id"],$_POST["name_editAddress"],$_POST["lastnames_editAddress"],
        $_POST["email_editAddress"],$_POST["phone_editAddress"],$_POST["country_editAddress"],$_POST["province_editAddress"],
        $_POST["address_editAddress"],$_POST["city_editAddress"],$_POST["postal_code_editAddress"],$_POST["nif_editAddress"],$_POST["created_at_editAddress"]);
    $address->id = $_POST["id_editAddress"];
    $address->updateChanges();
    header("location: ../pages/botiga_view/profile.php");
}

