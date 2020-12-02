<?php
include_once('../../modals/Database.php');
include_once ('../../controllers/AdminTokenController.php');
include_once ("../modals/Product.php");
include_once ("../dependencies/Aws/Aws/S3/Exception/S3Exception.php");
include_once ("../dependencies/Aws/Aws/S3/S3Client.php");
session_start();

$_SESSION["test"] = "test";

if(isset($_POST["category_newProduct"]) && isset($_POST["name_newProduct"]) && isset($_POST["description_newProduct"])){
 //   $product = new Product($_POST["name_newProduct"],$_POST["description_newProduct"],)
}

if(isset($_FILES)){
    echo "dheudfuehud";
    die();
    $s3 = new \Aws\S3\S3Client([
        'version' => 'latest',
        'region' => 'us-east-1',
        'credentials'=>[
          'key' => 'ASIAVUHCLKEGXFFYKMQW',
          'secret' => '2YWBt1WfYaiHchDp6AVmMTJnTK0+NDxCelU/iCBq',
          'token' => 'FwoGZXIvYXdzEDEaDJOlX6qcbI9BYhKciSLKAemBjSZnQLDcd1Wbq5WGgFsXt01TJdqxv4WqoLlFDlOIE9RNIGZxZFzYawIoIwA5eH05QwcMNGgK7sG4t3MBXBCeEUDcgLZZgTSj2scm72zw8TcA9Tr1WCriZkQUTbz9KoMaIoj8w8kYS52tOt7KIR0kpe1O59xRGKcHypfCzz/cEbVwflCuqPLcukSxHtJK4KgYmkp7RNxC9X1ku2Ln8WaC8EbqMO68vVFb5W9BNg5It2kr1V2/Uj0Rjuw6tagVGPLoqTDYAzl6M4kokeKe/gUyLdN547IHRo/DTy2UHKTGU7u/hJ8sG1/gaa+w3oV1VFME/naSRsfeXVTSpaw2hQ=='
        ],
    ]);
    $bucket = 'shop-php';
    $keyname = "my_object";

    try{
        $result = $s3->putObject([
            'Bucket' => $bucket,
            'Key' =>$_FILES["uploadfile"]["name"],
            'Body' => 'hello',
            'ContentType' =>$_FILES["uploadfile"]["type"],
            'ACL' => 'public-read',
            'StorageClass' => 'REDUCED_REDUNDANCY',
            'SourceFile' => $_FILES["uploadfile"]["tmp_name"]
        ]);

    } catch (\Aws\S3\Exception\S3Exception $e){
        echo $e->getMessage().PHP_EOL;
    }
}
echo "hi";
die();