<?php
require 'modules/apiCore.php';

$data = json_decode(file_get_contents("php://input"));

$lang = isset($data->lang) ? filterData($data->lang) : "";
$type = isset($data->type) ? filterData($data->type) : "";
$os_version = isset($data->os_version) ? filterData($data->os_version) : "";
$carrier = isset($data->carrier) ? filterData($data->carrier) : "";
$app_version = isset($data->app_version) ? filterData($data->app_version) : "";
$model = isset($data->model) ? filterData($data->model) : "";
$firebaseKey = isset($data->firebaseKey) ? filterData($data->firebaseKey) : "";

$sql = "INSERT INTO devices_info (lang, type, os_version, carrier, app_version, model, app_member_id, firebase_key) VALUES 
            ('$lang', '$type', '$os_version', '$carrier', '$app_version', '$model', '', '$firebaseKey')";
if(mysqli_query($con, $sql)){
    $currentUserId = mysqli_insert_id($con);
    $response["Error"] = false;
    $response["Info"]["msg"] = "New device added successfully";
}else{
    $response["Error"] = true;
    $response["Info"]["msg"] = mysqli_error($con);
    echo json_encode($response); exit(); die();
}
$response["deviceID"] = $currentUserId;

echo json_encode($response);