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
   // unset($_SESSION["images_newProduct"]);
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
        $productInfo = $database->executeQuery("SELECT MAX(id) FROM products",array());
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
                'key'=> 'ASIAVUHCLKEG6JNNAUVL',
                'secret' => 'T+AdC6y0nHnZp0sBjN/pHpJ9SuJ6RvdM+thV3usB',
                'token' => 'FwoGZXIvYXdzEGMaDIEa2NV/1VXQ2EngCyLKAbMDLfkT4ibeAw0eLvdmnySBDgN8N5MLsyfGVSW0RIuM28YZlGoxnEQ7vSqiURiVRqp+UOvLV0idn6YsA1UzpekLPm/HrIXtrr1khBLDee46F3aqX/27i7NWkMqycu/kM0ztr6E78UEciAReDCeQQ9yjZ5Rkar7BuvS9I8DHfsiac9317sm6ydRh/qGUSrBDYTYtDTxQCebxUgc7aEF7aHBb+HvrDxoeHRPz0x6Q4ecYBl0MiZcIfH+lxzuzccNSIBXvKOc+Shvr+o4oj+Sp/gUyLbSoGTyPqVzJwnyI1L2rl5CjU6ukRVRRz1LEq0rjQbHNd3fYE9TDKeSFm9t1QQ=='
            ]
        ]);
        foreach ($_SESSION["images_newProduct"] as $image){
            var_dump($image);
            die();
            try {
                $image_parts = explode(";base64,",$image->dataURL);
                $image_base64 = base64_decode($image_parts[1]);
                $result = $s3->putObject([
                    'Bucket' => $bucket,
                    'Key'    => $image->file_name,
                    'Body'   => $image_base64,
                    'ContentType'   => $image->type,
                    'ACL'    => 'public-read',
                    'StorageClass'   => 'REDUCED_REDUNDANCY',
                ]);

                echo $result['ObjectURL'].PHP_EOL;
            }catch (S3Exception $e) {
                echo $e->getMessage() . PHP_EOL;
                die();
            }
        }
    }
}


