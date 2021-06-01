<?php
require 'modules/apiCore.php';

$data = json_decode(file_get_contents("php://input"));

$uniqueID = isset($data->uniqueID) ? filterData($data->uniqueID) : "";
$lang = isset($data->lang) ? filterData($data->lang) : "";
$type = isset($data->type) ? filterData($data->type) : "";
$os_version = isset($data->os_version) ? filterData($data->os_version) : "";
$carrier = isset($data->carrier) ? filterData($data->carrier) : "";
$app_version = isset($data->app_version) ? filterData($data->app_version) : "";
$model = isset($data->model) ? filterData($data->model) : "";
$appMemberId = isset($data->appMemberId) ? filterData($data->appMemberId) : "";

$sql = "SELECT * FROM devices_info WHERE unique_id =$uniqueID LIMIT 1";
$result = mysqli_query($con, $sql);
if(mysqli_num_rows($result)){ //If id already exists do nothing
    $response["Error"] = false;
    $response["Body"]["msg"] = "Device info already exists on server.";
}else{ // If its new ID, insert data into DB
    $sql = "INSERT INTO devices_info (lang, type, os_version, carrier, app_version, model, app_member_id) VALUES 
            ('$lang', '$type', '$os_version', '$carrier', '$app_version', '$model', '$appMemberId')";
    echo $sql;
    if(mysqli_query($con, $sql)){
        $response["Error"] = false;
        $response["Body"]["msg"] = "New device added successfully";
    }else{
        $response["Error"] = true;
        $response["Body"]["msg"] = mysqli_error($con);
    }
}

echo json_encode($response);