<?php
include('../../controllers/UserFunctions.php');
include ('../../modals/Database.php');
session_start();

unset($_SESSION["token_login"]);
unset($_SESSION["user_id"]);
unset($_COOKIE["token_login"]);
unset($_COOKIE["user_id"]);
if(!isset($_GET["id"]) || !isset($_GET["token_pass"]) || checkUserActivated($_GET["id"],$_GET["token_pass"])){
    header("location: ./login.php");
} else{
    activeUser($_GET["id"],$_GET["token_pass"]);
    $_SESSION["email_message"] = "<strong>Usuari activat!</strong> Fés login per accedir a les funcions del teu usuari.";
    header("location: ../botiga_view/index.php");
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="./css/finish_buy.css">
    <script src="https://kit.fontawesome.com/e7269a261c.js" crossorigin="anonymous"></script>
    <title>Eshop</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../botiga_view/images/favicon.png">
</head>
<body>
<div class="check">
    <p><i class="fas fa-check-circle icon-check"></i><span class="compra-text">Usuari activat correctament</span></p>
    <button class="button-close" onclick="window.location.href = '../botiga_view/index.php'">Anar a la botiga</button>
</div>
</body>
</html>
