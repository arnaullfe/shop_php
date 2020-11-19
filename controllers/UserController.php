<?php
include_once ("../controllers/MainController.php");
include_once ("../modals/Database.php");
session_start();

if(isset($_POST["name_register"])){
    $database = new Database();
    unset($_SESSION["register_errors"]);
    unset($_SESSION["name_register"]);
    unset($_SESSION["lastnames_register"]);
    unset($_SESSION["email_register"]);
    unset($_SESSION["password_register"]);
        $vars = array(
            "data"=>array($_POST["name_register"],$_POST["lastnames_register"],$_POST["email_register"],$_POST["password_register"],$_POST["password_confirm_register"],$_POST["g-recaptcha-response"]),
            "names"=>array("name_register","lastnames_register","email_register","password_register","password_confirm_register","recaptcha_register"));
       $errors = checkPostRequest($vars);
       //var_dump($errors);
       if(count($errors)==0){
           echo "bé";
       }else{
           $_SESSION["register_errors"] = $errors;
           $_SESSION["name_register"] = $_POST["name_register"];
           $_SESSION["lastnames_register"] = $_POST["lastnames_register"];
           $_SESSION["email_register"] = $_POST["email_register"];
           $_SESSION["password_register"] = $_POST["password_register"];
           header("location: ../pages/admin_view/register.php");
       }
}