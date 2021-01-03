<?php
include_once ("../modals/ProductCategory.php");
include_once ("../modals/Database.php");
date_default_timezone_set('Europe/Madrid');
session_start();

if(isset($_POST["name_productCategory"]) && isset($_POST["description_productCategory"])){
    $category = new ProductCategory($_POST["name_productCategory"],$_POST["description_productCategory"]);
    $database = new Database();
    $database->executeQuery("INSERT INTO productCategory (name,description,last_modified,created_at) VALUES (?,?,?,?)",$category->getDatabaseValues());
    $database->closeConnection();
    $_SESSION["message"] = "<strong>Èxit!</strong> La categoria s'ha creat correctament!";
    header("location: ../pages/admin_view/list-categories.php");
}

if(isset($_POST["id_changeState"]) && isset($_POST["status_category"])){
    $database = new Database();
    $date = new DateTime();
    $date = date_format($date, "Y-m-d H:i:s");
    $database->executeQuery("UPDATE productCategory SET activated=? WHERE id=?",array($_POST["status_category"],$_POST["id_changeState"]));
    $database->closeConnection();
}

if(isset($_POST["id_edit_productCategory"])){
    $database = new Database();
    $database->executeQuery("UPDATE productCategory set name=?, description=? WHERE id=?",array($_POST["name_edit_productCategory"],$_POST["description_edit_productCategory"],$_POST["id_edit_productCategory"]));
    $database->closeConnection();
    $_SESSION["message"] = "<strong>Èxit!</strong> La categoria modificada correctament!";
    header("location: ../pages/admin_view/list-categories.php");
}

