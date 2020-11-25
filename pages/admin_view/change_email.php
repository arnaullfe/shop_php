<?php
include('../../controllers/UserFunctions.php');
include ('../../modals/Database.php');
if(!isset($_GET["id"]) || !isset($_GET["token_pass"]) || checkUserActivated($_GET["id"],$_GET["token_pass"])){
    header("location: ./login.php");
} else{
    activeUser($_GET["id"],$_GET["token_pass"]);
    $_SESSION["email_message"] = "<strong>Email Canviat!</strong> Ja podràs veure l'email del teu usuari canviat.";
    header("location: ../botiga_view/index.php");
}