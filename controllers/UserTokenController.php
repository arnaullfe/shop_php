<?php
session_start();
if(isset($_COOKIE["token_login"]) && isset($_COOKIE["user_id"])){
    $_SESSION["token_login"] = $_COOKIE["token_login"];
    $_SESSION["user_id"] = $_COOKIE["user_id"];
}


if(isset($_SESSION["token_login"]) && isset($_SESSION["user_id"])){
    $database = new Database();
    $result = $database->executeQuery("SELECT * FROM users WHERE token_login=? AND id=? AND role=?",array($_SESSION["token_login"],$_SESSION["user_id"],0));
    $database->closeConnection();
    if(count($result)==0){
        deleteCokiesAndSession();
    }
} else{
        deleteCokiesAndSession();
}

function deleteCokiesAndSession(){
    unset($_SESSION["token_login"]);
    unset($_SESSION["user_id"]);
    unset($_COOKIE["token_login"]);
    unset($_COOKIE["user_id"]);
}
