<?php
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
