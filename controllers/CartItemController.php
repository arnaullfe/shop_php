<?php
include_once ('../modals/CartItem.php');
include_once ('../modals/Database.php');
include_once ('./MainController.php');
session_start();
if(isset($_POST["product_id_addCart"])){
    if(!isset($_SESSION["user_id"])){
        header("location: ../pages/admin_view/login.php");
    } else{
        $database = new Database();
        $cartItems = $database->executeQuery("SELECT * FROM cartItems WHERE cart_id = (SELECT id FROM carts WHERE user_id=? ) AND product_id = ?",array($_SESSION["user_id"],$_POST["product_id_editCart"]));
        if(count($cartItems)>0){
            $database->executeQuery("UPDATE cartItems SET units=? WHERE id=?",array($_POST["units_addCart"],$cartItems[0]["id"]));
        }else{
            $database->executeQuery("INSERT INTO cartItems (cart_id,product_id,units,created_at) VALUES ((SELECT id FROM carts WHERE user_id=?))",array($_SESSION["user_id"],$_POST["product_id_editCart"],$_POST["units_editCart"],getCurrentDateTime()));
        }
        $cartId = $database->executeQuery('SELECT id FROM carts WHERE user_id=?',array($_SESSION["user_id"]));
        header("location: ../pages/botiga_view/cart.php?cart_id=".$cartId);
    }

}

if(isset($_POST["product_id_editCart"])){
    $error = false;
    if(!isset($_SESSION["user_id"]) ){
        header("location: ../pages/admin_view/login.php");
    } else{
        $database = new Database();
        $cartItems = $database->executeQuery("SELECT * FROM cartItems WHERE cart_id = (SELECT id FROM carts WHERE user_id=? ) AND product_id = ?",array($_SESSION["user_id"],$_POST["product_id_editCart"]));
        if(count($cartItems)>0){
            $units = $cartItems[0]["units"] + $_POST["units_editCart"];
            if(checkUnits($_POST["product_id_editCart"],$units)==true){
                $database->executeQuery("UPDATE cartItems SET units=? WHERE id=?",array($units,$cartItems[0]["id"]));
            }else{
                $error= true;
            }
        }else{
            if(checkUnits($_POST["product_id_editCart"],$_POST["units_editCart"])==true){
                $database->executeQuery("INSERT INTO cartItems (cart_id,product_id,units,created_at) VALUES ((SELECT id FROM carts WHERE user_id=?),?,?,?)",array($_SESSION["user_id"],$_POST["product_id_editCart"],$_POST["units_editCart"],getCurrentDateTime()));
            }else{
                $error= true;
            }
        }
        if($error==false){
            $cartId = $database->executeQuery('SELECT id FROM carts WHERE user_id=?',array($_SESSION["user_id"]));
            header("location: ../pages/botiga_view/cart.php?cart_id=".$cartId[0]["id"]);
        } else{
            $units_product = $database->executeQuery("SELECT units FROM products WHERE id = ?",array($_POST["product_id_editCart"]));
            $_SESSION["error_message"] = "<strong>Error!</strong>No hi ha prous unitats, pots afegir màxim ".$units_product[0]['units']." unitats";
            header("location: ../pages/botiga_view/cart.php?product_id=".$_POST["product_id_editCart"]);
        }
        $database->closeConnection();
    }
}

if(isset($_POST["add_one_cart_id"])){
    unset($_SESSION["message_error"]);
    $database = new Database();
    $units_product = $database->executeQuery("SELECT units FROM products WHERE id=?",array($_POST["add_one_product_id"]))[0]['units'];
    $units = $database->executeQuery('SELECT units FROM cartItems WHERE cart_id=? AND product_id=?',array($_POST["add_one_cart_id"],$_POST["add_one_product_id"]))[0]['units'];
    if(($units+1)<=$units_product){
        $database->executeQuery("UPDATE cartItems SET units = units+1 WHERE cart_id=? AND product_id=?",array($_POST["add_one_cart_id"],$_POST["add_one_product_id"]));
        $database->closeConnection();
    }else{
        $_SESSION["message_error"] = "<strong>Error!</strong>No hi ha prous unitats";
    }
    header("location: ../pages/botiga_view/cart.php?cart_id=".$_POST["add_one_cart_id"]);
}

if(isset($_POST["minus_one_cart_id"])){
    $database = new Database();
    $units = $database->executeQuery('SELECT units FROM cartItems WHERE cart_id=? AND product_id=?',array($_POST["minus_one_cart_id"],$_POST["minus_one_product_id"]))[0]['units'];
    if($units>1){
        $database->executeQuery("UPDATE cartItems SET units = units-1 WHERE cart_id=? AND product_id=?",array($_POST["minus_one_cart_id"],$_POST["minus_one_product_id"]));
    } else{
        $database->executeQuery("DELETE FROM cartItems WHERE cart_id=? AND product_id=?",array($_POST["minus_one_cart_id"],$_POST["minus_one_product_id"]));
    }
    $database->closeConnection();
    header("location: ../pages/botiga_view/cart.php?cart_id=".$_POST["minus_one_cart_id"]);
}

if(isset($_GET["product_id_deleteCart"])){
    $database = new Database();
    $database->executeQuery("DELETE FROM cartItems WHERE product_id=? AND cart_id=(SELECT id FROM carts WHERE user_id=?)",array($_GET["product_id_deleteCart"],$_SESSION["user_id"]));
    $cart_id = $database->executeQuery('SELECT id FROM carts WHERE user_id=?',array($_SESSION["user_id"]))[0]['id'];
    $database->closeConnection();
    header("location: ../pages/botiga_view/cart.php?cart_id=$cart_id");
}



function checkUnits($product_id,$units){
    $database = new Database();
    $units_product = $database->executeQuery("SELECT units FROM products WHERE id=?",array($product_id));
    $database->closeConnection();
    if(count($units_product)>0 && $units_product[0]["units"]>=$units){
        return true;
    }
    return false;
}

