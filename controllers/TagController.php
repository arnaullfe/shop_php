<?php
include_once ("../modals/Database.php");
include_once ("../modals/Tag.php");

if(isset($_POST["name_newTag"]) && isset($_POST["color_newTag"])){
    $tag = new Tag($_POST["name_newTag"],$_POST["color_newTag"],null);
    $database = new Database();
    $database->executeQuery("INSERT INTO tags (name,color,created_at) VALUES (?,?,?)",$tag->getDatabaseValues());
    $database->closeConnection();
    header("location: ../pages/admin_view/list-tags.php");
}

if(isset($_POST["id_editTag"])){
    $database = new Database();
    $database->executeQuery("UPDATE tags  set name=?,color=? WHERE id =?",array($_POST["name_editTag"],$_POST["color_editTag"],$_POST["id_editTag"]));
    $database->closeConnection();
    header("location: ../pages/admin_view/list-tags.php");
}