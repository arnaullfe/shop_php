<?php
date_default_timezone_set('Europe/Madrid');
session_start();

function checkPostRequest($data)
{
    $errors = [];
    $count_data = count($data["data"]);
    $count_names = count($data["names"]);
    if ($data == null || $count_data == 0) {
        return [];
    } else if ($count_data == $count_names) {
        for ($i = 0; $i < $count_data; $i++) {
            if (strlen(trim($data["data"][$i])) == 0) {
                array_push($errors, "error_".$data["names"][$i]);
            } 
        }
        return $errors;
    }
    return $data["names"];

}

function checkOnlyLetters($string){
    if (preg_match('/^[A-Za-z]+$/', $string)) {
        return true;
    }
    return false;
}

function checkEmail($email){
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
      }
        return false;
}

function comparePasswords($pasword,$confirm){
    if(strcmp($pasword,$confirm)){
        return true;
    }
    return false;
}

function getCurrentDateTime(){
    $date = new DateTime();
    return $date->format("Y-m-d H:i:s");
}

function rangeDateTimeToArray($string){
    $array = [];
    $explode = explode("-",trim($string));
    foreach ($explode as $expl){
        $split = explode("/",$expl);
        $hour_split = explode(" ",$split[2]);
        $date = new DateTime(trim($hour_split[0])."-".trim($split[1])."-".trim($split[0])." ".trim($hour_split[1]));
        array_push($array,$date);
    }
    return $array;
}

function calculateNewPrice($price, $discount)
{
    return $price - ($price * ($discount / 100));
}

function formatPrice($price){
    return number_format($price, 2, ",", ".");
}

function calculateItemsPrices($items){
    $final_price = 0;
    foreach ($items as $item){
        $item_price = $item["product_price"]*$item["units"];
        if(isset($item["discount"]) && $item["discount"]!=null){
            $item_price = (calculateNewPrice($item["product_price"],$item["discount"]))*$item["units"];
        }
        $final_price += $item_price;
    }
    return $final_price;
}

function calculatSave($items){
    $save = 0;
    foreach ($items as $item){
        if(isset($item["discount"]) && $item["discount"]!=null){
            $item_price_discount = ($item["product_price"]-calculateNewPrice($item["product_price"],$item["discount"]));
            $save += ($item_price_discount*$item["units"]);
        }
    }
    if(calculateItemsPrices($items)>=50){
        $save+=5;
    }
    return $save;
}
