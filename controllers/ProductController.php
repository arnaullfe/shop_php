<?php
include_once('../modals/Database.php');
include_once ('../controllers/AdminTokenController.php');
include_once ("../modals/Product.php");
include_once ("./MainController.php");
include_once ("../dependencies/Aws/vendor/autoload.php");
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
date_default_timezone_set('Europe/Madrid');
session_start();

if(isset($_POST["image_newProduct"])){
    $_POST["image_newProduct"] = json_decode($_POST["image_newProduct"]);
    $name = round(microtime(true) * 1000)."_".$_POST["image_newProduct"]->name_file;
    if(!isset($_SESSION["images_newProduct"])){
        $_SESSION["images_newProduct"] = array();
    }
    $var = array("id_temp"=> $_POST["image_newProduct"]->upload->uuid ,"file"=>$_POST["image_newProduct"],"file_name"=>$name);
    array_push($_SESSION["images_newProduct"],$var);
    echo json_encode($var);
}


if(isset($_POST["name_newProduct"])){
    unset($_SESSION["errors_newProduct"]);
    unset($_SESSION["name_newProduct"]);
    unset($_SESSION["description_newProduct"]);
    unset($_SESSION["units_newProduct"]);
    unset($_SESSION["price_newProduct"]);
    $activated = 1;
    if(!isset($_POST["activated_newProduct"])){
        $activated = 0;
    }
    $vars = array(
        "data" => array($_POST["name_newProduct"], $_POST["description_newProduct"], $_POST["units_newProduct"], $_POST["price_newProduct"]),
        "names" => array("name_newProduct", "description_newProduct", "units_newProduct", "price_newProduct")
    );
    $errors = checkPostRequest($vars);
    if(count($errors)==0){
        $product = new Product($activated,$_POST["name_newProduct"],$_POST["description_newProduct"],abs(intval($_POST["units_newProduct"])),$_POST["priceIva_type_newProduct"],abs($_POST["price_newProduct"]),$_POST["iva_newProduct"],$_POST["category_newProduct"]);
        $database = new Database();
        $database->executeQuery("INSERT INTO products (activated,name,description,units,category_id,iva,price_iva,price_no_iva,created_at,last_modified) VALUES(?,?,?,?,?,?,?,?,?,?)",$product->getDatabaseValues());
        $database->closeConnection();
        $database = new Database();
        $productInfo = $database->executeQuery("SELECT MAX(id) as id FROM products",array());
        $database->closeConnection();
        uploadImages($productInfo[0]["id"]);
        $_SESSION["message"] = "El producte<strong> ".$_POST["name_newProduct"]." </strong> s'ha creat correctament";
        header("location: ../pages/admin_view/list-products.php");
    } else{
        $_SESSION["errors_newProduct"] = $errors;
        $_SESSION["name_newProduct"] = $_POST["name_newProduct"];
        $_SESSION["description_newProduct"] = $_POST["description_newProduct"];
        $_SESSION["units_newProduct"] = $_POST["units_newProduct"];
        $_SESSION["price_newProduct"] = $_POST["price_newProduct"];
        $_SESSION["category_newProduct"] = $_POST["category_newProduct"];
        $_SESSION["priceIva_type_newProduct"] = $_POST["priceIva_type_newProduct"];
        $_SESSION["iva_newProduct"] = $_POST["iva_newProduct"];
        header("location: ../pages/admin_view/new-product.php");
    }
}

if(isset($_POST["delete_image_newProduct"])){
    for($i=0;$i<count($_SESSION["images_newProduct"]);$i++){
        if($_SESSION["images_newProduct"][$i]["id_temp"]==$_POST["delete_image_newProduct"]){
            array_splice($_SESSION["images_newProduct"],$i,1);
        }
    }
    echo json_encode($_POST["delete_image_newProduct"]);
}




function uploadImages($id){
    if(isset($_SESSION["images_newProduct"]) && count($_SESSION["images_newProduct"])>0){
        $bucket = "shop-php";
        $s3 = new S3Client([
            'version'=> 'latest',
            'region'=> 'us-east-1',
            'credentials'=>[
                'key'=> 'ASIAVUHCLKEGSMXD5ZOH',
                'secret' => 'H9flYhDQDsF7WMJMqRNX64uOpk204p/D6jYKrrZW',
                'token' => 'FwoGZXIvYXdzEGgaDOY/a6IgObj9+SwEpSLKAYmBATyZjUNWqljMpsLhZeLTeq6TR1P/hI8HNyoIFx82hBiEbstn5v46xLGvc7y1/3z5cu9qrgwQwqsYJjHPNpZqvWr4BVuv/gaONcTNoFEUTq4dMsqvHM8bhnlpqt38b5I3DI8v/K1JOav1yT6knACJMxfDByY4baDFZy9dAFaIOWB5XxjRyBXL+VZV6Mk84T8TI7CwBfe0rfw7PEANDDdsexDYUvwxzEo6/9T+tlXIsKHsEp3TwoGqC0clEq4PZw5jwaOk9J9yJUwoy/Kq/gUyLU3qwXCMQa+oht9dcuk1ZaEKqD/Djt32lwhqD2fnREZvbtYzvdFo5Jh9El1ZuQ=='
            ]
        ]);
        $database = new Database();
        foreach ($_SESSION["images_newProduct"] as $image){
            try {
                $image_parts = explode(";base64,",$image["file"]->dataURL);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $result = $s3->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $image["file_name"],
                    'Body'   => $image_base64,
                    'ContentType'   => 'image/' . $image_type,
                    'ACL'    => 'public-read',
                    'StorageClass'   => 'REDUCED_REDUNDANCY',
                ]);

                $date = new DateTime();
                $database->executeQuery("INSERT INTO images_product (id_product,url,name,created_at) VALUES(?,?,?,?)",array($id,$result['ObjectURL'],$image["file_name"],$date->format("Y-m-d H:i:s")));
            }catch (S3Exception $e) {
                echo $e->getMessage() . PHP_EOL;
                die();
            }
        }
        $database->closeConnection();
        unset($_SESSION["images_newProduct"]);
    }
}


