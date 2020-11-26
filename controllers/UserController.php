<?php
require_once("./MainController.php");
require_once("../modals/Database.php");
require_once("../modals/User.php");
require_once("./MailController.php");
date_default_timezone_set('Europe/Madrid');
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
    $num_emails = $database->executeQuery("SELECT count(*) as num FROM users WHERE email LIKE ?", array($_POST["email_register"]))[0]["num"];
    $database->closeConnection();
    if (checkEmail($_POST["email_register"]) == false || $num_emails > 0) {
        array_push($errors, "error_email_register");
    }

    if (strlen($_POST["password_register"]) < 5) {
        array_push($errors, "error_password_register");
    }

    if (comparePasswords($_POST["password_register"], $_POST["password_confirm_register"])) {
        array_push($errors, "error_password_confirm_register");
    }

    if (count($errors) == 0) {
        $user = new User($_POST["name_register"], $_POST["lastnames_register"], $_POST["email_register"], password_hash($_POST["password_register"], PASSWORD_DEFAULT));
        $database = new Database();
        $database->executeQuery("INSERT INTO users (email,name,lastnames,password,role,banned,activated,last_session,token_login,token_pass,image) values (?,?,?,?,?,?,?,?,?,?,?)", $user->getDataInsertSql());
        $user->setId($database->executeQuery("SELECT id FROM users WHERE email = ?", array($_POST["email_register"]))[0]["id"]);
        sendMailActivatedUser($user);
        $database->closeConnection();
        $_SESSION["token_login"] = $user->getTokenLogin();
        $_SESSION["user_id"] = $user->getId();
        $_SESSION["email_message"] = "<strong>Registre!</strong> Activa el teu usuari amb l'email que t'hem enviat.";
        header("location: ../index.php");
    } else {
        $_SESSION["register_errors"] = $errors;
        $_SESSION["name_register"] = $_POST["name_register"];
        $_SESSION["lastnames_register"] = $_POST["lastnames_register"];
        $_SESSION["email_register"] = $_POST["email_register"];
        $_SESSION["password_register"] = $_POST["password_register"];
        header("location: ../pages/admin_view/register.php");
    }
}

if (isset($_POST["email_recover"])) {
    unset($_SESSION["email_recover"]);
    unset($_SESSION["recover_errors"]);
    if (checkEmail($_POST["email_recover"])) {
        $database = new Database();
        $num = $database->executeQuery("SELECT * FROM users WHERE email = ?", array($_POST["email_recover"]));
        if (count($num) == 0) {
            $_SESSION["recover_errors"] = array("error_email_exists_recover");
            $_SESSION["email_recover"] = $_POST["email_recover"];
            header("location: ../pages/admin_view/forgot-password.php");
        } else {
            $user = new User($num[0]["name"], $num[0]["lastnames"], $num[0]["email"], $num[0]["password"]);
            $user->setRole($num[0]["role"]);
            $user->setBanned($num[0]["banned"]);
            $user->setActivated($num[0]["activated"]);
            $user->setLastSession($num[0]["last_session"]);
            $user->setTokenLogin($num[0]["token_login"]);
            $database->executeQuery("UPDATE users SET token_pass=? WHERE email=?", array($user->getTokenPass(), $user->getEmail()));
            $user->setId($database->executeQuery("SELECT id FROM users WHERE token_pass=? AND email=?", array($user->getTokenPass(), $user->getEmail()))[0]["id"]);
            sendMailRecoverPassword($user);
            $_SESSION["email_message"] = "<strong>Email enviat!</strong> Revisa el correct electrònic per canviar la contrasenya.";
            header("location: ../pages/botiga_view/index.php");
        }
        $database->closeConnection();
    } else {
        $_SESSION["recover_errors"] = array("error_email_format_recover");
        $_SESSION["email_recover"] = $_POST["email_recover"];
        header("location: ../pages/admin_view/forgot-password.php");
    }
}

if (isset($_POST["recover_password"])) {
    unset($_SESSION["recover_password"]);
    unset($_SESSION["recover_errors"]);
    if (strlen($_POST["recover_password"]) < 5) {
        $_SESSION["recover_errors"] = ["error_password_recover"];
        $_SESSION["recover_password"] = $_POST["recover_password"];
        header("location: ../pages/admin_view/recover_password.php?id=" . $_POST["recover_id"] . "&token_pass=" . $_POST["recover_token_pass"]);
    } else if ($_POST["recover_password"] !== $_POST["recover_password_confirm"]) {
        $_SESSION["recover_errors"] = ["error_password_confirm_recover"];
        $_SESSION["recover_password"] = $_POST["recover_password"];
        header("location: ../pages/admin_view/recover_password.php?id=" . $_POST["recover_id"] . "&token_pass=" . $_POST["recover_token_pass"]);
    } else {
        $password = password_hash($_POST["recover_password"], PASSWORD_DEFAULT);
        $database = new Database();
        $database->executeQuery("UPDATE users SET password = ?,token_pass = ?, token_login = ? WHERE id=? AND token_pass = ?", array($password, null, null, $_POST["recover_id"], $_POST["recover_token_pass"]));
        $database->closeConnection();
        $_SESSION["email_message"] = "<strong>Contrasenya canviada correctament!</strong> Fés login per accedir a les funcions del teu usuari.";
        header("location: ../pages/admin_view/login.php");
    }
}

if (isset($_POST["email_login"])) {
    unset($_SESSION["login_email"]);
    unset($_SESSION["login_errors"]);
    unset($_SESSION["login_remember"]);
    $errors = array();
    $database = new Database();
    $user_info = $database->executeQuery("SELECT * FROM users WHERE email LIKE ?", array($_POST["email_login"]));
    if (count($errors) == 0 && checkEmail($_POST["email_login"]) == false || count($user_info) == 0) {
        array_push($errors, "error_email_login");
    } else if (strlen($_POST["password_login"]) == 0 || password_verify($_POST["password_login"], $user_info[0]["password"]) == false) {
        array_push($errors, "error_password_login");
    } else if ($user_info[0]["banned"] == true) {
        array_push($errors, "error_banned_login");
    }
    if (count($errors) == 0) {
        $token = bin2hex(random_bytes(16));
        $date = new DateTime();
        $date = date_format($date, "Y-m-d H:i:s");
        $database->executeQuery("UPDATE users SET token_login = ?, last_session =? WHERE id=?", array($token, $date, $user_info[0]["id"]));
        unset($_COOKIE["token_login"]);
        unset($_COOKIE["user_id"]);
        if (isset($_POST["remember_login"])) {
            setcookie("token_login", $token, time() + (86400 * 30), "/");
            $_COOKIE["user_id"] = $user_info[0]["id"];
        }
        $_SESSION["token_login"] = $token;
        $_SESSION["user_id"] = $user_info[0]["id"];
        if ($user_info[0]["role"] == 1 || $user_info[0]["role"] == 2) {
            header("location: ../pages/admin_view/index.php");
        } else {
            header("location: ../pages/botiga_view/index.php");
        }
    } else {
        if (isset($_POST["remember_login"])) {
            $_SESSION["login_remember"] = true;
        } else {
            $_SESSION["login_remember"] = false;
        }
        $_SESSION["login_email"] = $_POST["email_login"];

        $_SESSION["login_errors"] = $errors;
        header("location: ../pages/admin_view/login.php");
    }
    $database->closeConnection();
}

if (isset($_POST["email_changes"]) || isset($_POST["name_changes"]) || isset($_POST["lastnames_changes"])) {
    unset($_SESSION["lastnames_changes"]);
    unset($_SESSION["name_changes"]);
    unset($_SESSION["email_changes"]);
    unset($_SESSION["changes_errors"]);
    unset($_SESSION["email_message"]);
    $errors = array();
    if (checkOnlyLetters($_POST["name_changes"]) == false) {
        array_push($errors, "error_name_changes");
    }
    if (checkOnlyLetters($_POST["lastnames_changes"]) == false) {
        array_push($errors, "error_lastnames_changes");
    }
    $database = new Database();
    $user_info = $database->executeQuery("SELECT * FROM users WHERE email LIKE ?", array($_POST["email_changes"]));
    $database->closeConnection();
    if (checkEmail($_POST["email_changes"]) == false || (count($user_info)>0 && $user_info[0]["id"]!=$_SESSION["user_id"])) {
        array_push($errors, "error_email_changes");
    }

    if (count($errors) == 0) {
        $database = new Database();
        $last_user = $database->executeQuery("SELECT * FROM users WHERE id=?", array($_SESSION["user_id"]));
        $database->closeConnection();
        $user = new User($_POST["name_changes"],$_POST["email_changes"],$_POST["email_changes"], $user_info[0]["password"]);
        $user->setId($_SESSION["user_id"]);
        if($_POST["name_changes"]!=$last_user[0]["name"] || $_POST["lastnames_changes"]!=$last_user[0]["lastnames"]){
            $database = new Database();
            $database->executeQuery("UPDATE users SET name = ?, lastnames = ?, image = ? WHERE id=? ",array($_POST["name_changes"],$_POST["lastnames_changes"],$user->getImage(),$_SESSION["user_id"]));
            $database->closeConnection();
            $_SESSION["changes_message"] = "<strong>Canvis realitzats correctament!</strong> El nom i els cognoms s'han canviat.";
        }

        if($_POST["email_changes"]!=$user_info[0]["email"]){
            $database = new Database();
            $database->executeQuery("UPDATE users SET token_pass = ?  WHERE id=? ",array($user->getTokenPass(),$_SESSION["user_id"]));
            $database->closeConnection();
            sendChangeEmailUser($user);
            $_SESSION["changes_email_message"] = "<strong>Atenció!</strong> Per aplicar els canvis en el correu, clica el botó de confirmar en el email enviat en el nou correu.";
        }

    } else {
        $_SESSION["changes_errors"] = $errors;
        $_SESSION["lastnames_changes"] = $_POST["lastnames_changes"];
        $_SESSION["name_changes"] = $_POST["name_changes"];
        $_SESSION["email_changes"] = $_POST["email_changes"];
    }

    header("location: ../pages/botiga_view/profile.php");
}

if(isset($_GET["id_ban"]) && isset($_GET["status_ban"])){
    $database = new Database();
    $database->executeQuery("UPDATE users SET banned=? WHERE id=?",array($_GET["status_ban"],$_GET["id_ban"]));
    $database->closeConnection();
    header("location: ../pages/admin_view/list-users.php");
}

if($_GET["id_delete"]){
    $database = new Database();
    $database->executeQuery("DELETE FROM users WHERE id=?",array($_GET["id_delete"]));
    $database->closeConnection();
    header("location: ../pages/admin_view/list-users.php");
}

if(isset($_GET["id_admin"]) && isset($_GET["status_admin"])){
    $database = new Database();
    $database->executeQuery("UPDATE users set role=? WHERE id=?",array($_GET["status_admin"],$_GET["id_admin"]));
    $database->closeConnection();
    echo "succes";
}