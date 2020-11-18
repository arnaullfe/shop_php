<?php
include_once ("../controllers/MainController.php");
if(isset($_POST["name_register"])){
    unset($_SESSION["register_errors"]);
        $vars = array(
            "data"=>array($_POST["name_register"],$_POST["lastnames_register"],$_POST["email_register"],$_POST["password_register"],$_POST["password_confirm_register"]),
            "names"=>array("name_register","lastnames_register","email_register","password_register","password_confirm_register"));
       $errors = checkPostRequest($vars);
       //var_dump($errors);
       if(count($errors)==0){
           echo "bé";
       }else{
           $_SESSION["register_errors"] = $errors;
           header("location: ../pages/admin_view/register.php");
       }
}