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

if(isset($_POST["image_editProduct"])){
    $_POST["image_editProduct"] = json_decode($_POST["image_editProduct"]);
    $name = round(microtime(true) * 1000)."_".$_POST["image_editProduct"]->name_file;
    if(!isset($_SESSION["images_editProduct"])){
        $_SESSION["images_editProduct"] = array();
    }
    $var = array("id_temp"=> $_POST["image_editProduct"]->upload->uuid ,"file"=>$_POST["image_editProduct"],"file_name"=>$name);
    array_push($_SESSION["images_editProduct"],$var);
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

if(isset($_POST["name_editProduct"])){
    unset($_SESSION["errors_editProduct"]);
    unset($_SESSION["name_editProduct"]);
    unset($_SESSION["description_editProduct"]);
    unset($_SESSION["units_editProduct"]);
    unset($_SESSION["price_editProduct"]);
    $activated = 1;
    if(!isset($_POST["activated_editProduct"])){
        $activated = 0;
    }
    $vars = array(
        "data" => array($_POST["name_editProduct"], $_POST["description_editProduct"], $_POST["units_editProduct"], $_POST["price_editProduct"]),
        "names" => array("name_editProduct", "description_editProduct", "units_editProduct", "price_editProduct")
    );
    $errors = checkPostRequest($vars);
    if(count($errors)==0){
        $product = new Product($activated,$_POST["name_editProduct"],$_POST["description_editProduct"],abs(intval($_POST["units_editProduct"])),$_POST["priceIva_type_editProduct"],abs($_POST["price_editProduct"]),$_POST["iva_editProduct"],$_POST["category_editProduct"]);
        $database = new Database();
        $values = $product->getDatabaseValues();
        array_push($values,$_POST["id_editProduct"]);
        $database->executeQuery("UPDATE products set activated=?,name=?,description=?,units=?,category_id=?,iva=?,price_iva=?,price_no_iva=?,created_at=?,last_modified=? WHERE id=?",$values);
        $database->closeConnection();
        uploadEditImages($_POST["id_editProduct"]);
        $_SESSION["message"] = "El producte<strong> ".$_POST["name_newProduct"]." </strong> s'ha editat correctament";
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

if(isset($_POST["id_edit_units"]) && isset($_POST["units_edit_units"])){
    if(filter_var($_POST["units_edit_units"], FILTER_VALIDATE_INT)==true && $_POST["units_edit_units"]!=''){
       $database = new Database();
       $product = $database->executeQuery("SELECT * FROM products WHERE id=?",array($_POST["id_edit_units"]));
       $total_units = intval($product[0]["units"])+intval($_POST["units_edit_units"]);
       if($total_units>=0){
           $database->executeQuery("UPDATE products set units=? WHERE id=?",array($total_units,$_POST["id_edit_units"]));
           $_SESSION["message"] = "<strong>Èxit!</strong> Unitats canviades correctament!";
       }else{
           $_SESSION["error_message"] = "<strong>Error!</strong> No hi ha prous unitats, no pots restar més de ".$product[0]["units"]." unitats";
       }
       $database->closeConnection();
    }else{
        $_SESSION["error_message"] = "<strong>Error!</strong> Introdueix un número enter per editar les unitats!";
    }
    header("location: ../pages/admin_view/list-products.php");
}

if(isset($_POST["delete_image_editProduct"]) && isset($_POST["url_image_editProduct"])){
    $database = new Database();
    $database->executeQuery("DELETE FROM images_product WHERE id=?",array($_POST["delete_image_editProduct"]));
    $database->closeConnection();
    for($i=0;$i<count($_SESSION["images_editProduct"]);$i++){
        if($_SESSION["images_editProduct"][$i]["id"]==$_POST["delete_image_editProduct"] && $_SESSION["images_editProduct"][$i]["url"]==$_POST["url_image_editProduct"]){
            array_splice($_SESSION["images_editProduct"],$i,1);
        }
    }
    $_SESSION["message"] = "<strong>Fotografia eliminada correctament!</strong>";
    echo json_encode($_SESSION["images_editProduct"]);
    header("location: ../pages/admin_view/list-products.php?product_id=".$_POST["product_edit_id"]);
}


function uploadImages($id){
    if(isset($_SESSION["images_newProduct"]) && count($_SESSION["images_newProduct"])>0){
        $bucket = "shop-php";
        $s3 = new S3Client([
            'version'=> 'latest',
            'region'=> 'us-east-1',
            'credentials'=>[
                'key'=> 'ASIAVUHCLKEGWUGIZG7E',
                'secret' => 'STawLGGKUJRCsoj1e/Z/gieRhmnYBqt43HaDqxg2',
                'token' => 'FwoGZXIvYXdzEPD//////////wEaDAeIE5xsVMEXluZKMyLKAd36q4fq85CJ4hcub2tskjQq0zYuXT+gme7t8CbtCb2cCd6j9ZaNsVzW+JhC5qPgoR8LMuiT61vhSel8c4MlxGtrabH/p/0trgtInUM7uXRxzq7Jigti63pI8gKo9gz7gDAie2sABnycj45a741TqKMK3T8ABDiaE4fI2sD36I/NQWYonux4T6nWH0059c3WlkPjNG5SK1duC4SUGeTsS4DzXiJHQv+Bapk3lTkdrdgvZB9dyxX8Yc+iBdc3C2MxrWdheyRkJ/FEFPco8urI/gUyLQ//kRIifnyPYD09/XegPNalKDKZzing5zKHt3/VSWluGF/fUow+1ZlKNl2lDw=='
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

function uploadEditImages($id){
    if(isset($_SESSION["images_editProduct"]) && count($_SESSION["images_editProduct"])>0){
        $bucket = "shop-php";
        $s3 = new S3Client([
            'version'=> 'latest',
            'region'=> 'us-east-1',
            'credentials'=>[
                'key'=> 'ASIAVUHCLKEGWUGIZG7E',
                'secret' => 'STawLGGKUJRCsoj1e/Z/gieRhmnYBqt43HaDqxg2',
                'token' => 'FwoGZXIvYXdzEPD//////////wEaDAeIE5xsVMEXluZKMyLKAd36q4fq85CJ4hcub2tskjQq0zYuXT+gme7t8CbtCb2cCd6j9ZaNsVzW+JhC5qPgoR8LMuiT61vhSel8c4MlxGtrabH/p/0trgtInUM7uXRxzq7Jigti63pI8gKo9gz7gDAie2sABnycj45a741TqKMK3T8ABDiaE4fI2sD36I/NQWYonux4T6nWH0059c3WlkPjNG5SK1duC4SUGeTsS4DzXiJHQv+Bapk3lTkdrdgvZB9dyxX8Yc+iBdc3C2MxrWdheyRkJ/FEFPco8urI/gUyLQ//kRIifnyPYD09/XegPNalKDKZzing5zKHt3/VSWluGF/fUow+1ZlKNl2lDw=='
            ]
        ]);
        $database = new Database();
        foreach ($_SESSION["images_editProduct"] as $image){
            if(!isset($image["id"])){
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
        }
        $database->closeConnection();
        unset($_SESSION["images_editProduct"]);
    }
}


