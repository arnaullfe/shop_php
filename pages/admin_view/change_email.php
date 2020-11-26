<?php
include('../../controllers/UserFunctions.php');
include ('../../modals/Database.php');
session_start();

if(!isset($_GET["id"]) || !isset($_GET["token_pass"])  || !isset($_GET["email"]) || checkIdTokenPassUser($_GET["id"],$_GET["token_pass"])==false){
    header("location: ./login.php");
} else{
    changeEmail($_GET["id"],$_GET["token_pass"],$_GET["email"]);
    $_SESSION["email_message"] = "<strong>Email Canviat!</strong> Ja podràs veure l'email del teu usuari canviat.";
    header("location: ../botiga_view/index.php");
}