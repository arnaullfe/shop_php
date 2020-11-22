<?php
function checkUserActivated($id,$token_pass){
    $database = new Database();
    $activated = $database->executeQuery("SELECT activated FROM users WHERE id=? AND token_pass=?",array($id,$token_pass));
    $database->closeConnection();
    if(count($activated)>0 && $activated[0]["activated"]==true){
        return true;
    }
    return false;
}

function activeUser($id,$token_pass){
    $database = new Database();
    $database->executeQuery("update users set activated = ?,token_pass = ? WHERE id=? and token_pass LIKE ?",array(1,null,$id,$token_pass));
    $database->closeConnection();
}

function checkTokenPass($id,$token_pass){
    $database = new Database();
    $activated = $database->executeQuery("SELECT * FROM users WHERE id=? AND token_pass=?",array($id,$token_pass));
    $database->closeConnection();
    if(count($activated)>0){
        return true;
    }
    return false;
}