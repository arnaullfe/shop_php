<?php
require_once("./MainController.php");
require_once("../modals/Database.php");
require_once("../modals/User.php");
require_once ("./MailController.php");
session_start();

if (isset($_POST["name_register"])) {
    unset($_SESSION["register_errors"]);
    unset($_SESSION["name_register"]);
    unset($_SESSION["lastnames_register"]);
    unset($_SESSION["email_register"]);
    unset($_SESSION["password_register"]);
    $vars = array(
        "data" => array($_POST["name_register"], $_POST["lastnames_register"], $_POST["email_register"], $_POST["password_register"], $_POST["password_confirm_register"], $_POST["g-recaptcha-response"]),
        "names" => array("name_register", "lastnames_register", "email_register", "password_register", "password_confirm_register", "recaptcha_register")
    );
    $errors = checkPostRequest($vars);

        if (checkOnlyLetters($_POST["name_register"]) == false) {
            array_push($errors, "error_name_register");
        }

        if (checkOnlyLetters($_POST["lastnames_register"]) == false) {
            array_push($errors, "error_lastnames_register");
        }
        $database = new Database();
        $num_emails = $database->executeQuery("SELECT count(*) as num FROM users WHERE email LIKE ?",array($_POST["email_register"]))[0]["num"];
        $database->closeConnection();
        if (checkEmail($_POST["email_register"]) == false || $num_emails>0) {
            array_push($errors, "error_email_register");
        }

        if (strlen($_POST["password_register"]) < 5) {
            array_push($errors, "error_password_register");
        }

        if (comparePasswords($_POST["password_register"], $_POST["password_confirm_register"])) {
            array_push($errors, "error_password_confirm_register");
        }

        if (count($errors) == 0) {
            $user = new User($_POST["name_register"],$_POST["lastnames_register"],$_POST["email_register"],password_hash($_POST["password_register"],PASSWORD_DEFAULT));
            $database = new Database();
            $database->executeQuery("INSERT INTO users (email,name,lastnames,password,role,banned,activated,last_session,token_login,token_pass) values (?,?,?,?,?,?,?,?,?,?)",$user->getDataInsertSql());
            $user->setId($database->executeQuery("SELECT id FROM users WHERE email = ?",array($_POST["email_register"]))[0]["id"]);
            sendMailActivatedUser($user);
            $database->closeConnection();
            header("location: ../index.php");
        } else{
            $_SESSION["register_errors"] = $errors;
            $_SESSION["name_register"] = $_POST["name_register"];
            $_SESSION["lastnames_register"] = $_POST["lastnames_register"];
            $_SESSION["email_register"] = $_POST["email_register"];
            $_SESSION["password_register"] = $_POST["password_register"];
            header("location: ../pages/admin_view/register.php");   
        }
}

if(isset($_POST["email_recover"])){
    unset($_SESSION["email_recover"]);
    unset($_SESSION["recover_errors"]);
    if(checkEmail($_POST["email_recover"])){
        $database = new Database();
        $num = $database->executeQuery("SELECT * FROM users WHERE email = ?",array($_POST["email_recover"]));
        if(count($num)==0){
            $_SESSION["recover_errors"] = array("error_email_exists_recover");
            $_SESSION["email_recover"] = $_POST["email_recover"];
            header("location: ../pages/admin_view/forgot-password.php");
        } else{
            $user = new User($num[0]["name"],$num[0]["lastnames"],$num[0]["email"],$num[0]["password"]);
            $user->setRole($num[0]["role"]);
            $user->setBanned($num[0]["banned"]);
            $user->setActivated($num[0]["activated"]);
            $user->setLastSession($num[0]["last_session"]);
            $user->setTokenLogin($num[0]["token_login"]);
            $database->executeQuery("UPDATE users SET token_pass=? WHERE email=?",array($user->getTokenPass(),$user->getEmail()));
            $user->setId($database->executeQuery("SELECT id FROM users WHERE token_pass=? AND email=?",array($user->getTokenPass(),$user->getEmail()))[0]["id"]);
            sendMailRecoverPassword($user);
            header("location: ../pages/botiga_view/index.php");
            echo "<script>alert('Correu enviat per recuperar la contrasenya')</script>";
        }
        $database->closeConnection();
    } else{
        $_SESSION["recover_errors"] = array("error_email_format_recover");
        $_SESSION["email_recover"] = $_POST["email_recover"];
        header("location: ../pages/admin_view/forgot-password.php");
    }
}

if(isset($_POST["recover_password"])){
    unset($_SESSION["recover_password"]);
    unset($_SESSION["recover_errors"]);
    if(strlen($_POST["recover_password"])<5){
        echo "inside";
        $_SESSION["recover_errors"] = ["error_password_recover"];
        $_SESSION["recover_password"] = $_POST["recover_password"];
        header("location: ../pages/admin_view/recover_password.php?id=".$_POST["recover_id"]."&token_pass=".$_POST["recover_token_pass"]);
    } else if($_POST["recover_password"]!==$_POST["recover_password_confirm"]){
        echo "inside 2";
        $_SESSION["recover_errors"] = ["error_password_confirm_recover"];
        $_SESSION["recover_password"] = $_POST["recover_password"];
        header("location: ../pages/admin_view/recover_password.php?id=".$_POST["recover_id"]."&token_pass=".$_POST["recover_token_pass"]);
    } else{
        $password = password_hash($_POST["password_register"],PASSWORD_DEFAULT);
        $database = new Database();
        $database->executeQuery("UPDATE users SET password = ?,token_pass = ?, token_login = ? WHERE id=? AND token_pass = ?",array($password,null,null,$_POST["recover_id"],$_POST["recover_token_pass"]));
        $database->closeConnection();
        header("location: ../pages/admin_view/login.php");
    }
}

