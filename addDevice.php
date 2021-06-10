<?php
require 'modules/apiCore.php';

$data = json_decode(file_get_contents("php://input"));


$uniqueID = isset($data->deviceUniqueID) ? filterData($data->deviceUniqueID) : 0;
$lang = isset($data->lang) ? filterData($data->lang) : "";
$type = isset($data->type) ? filterData($data->type) : "";
$os_version = isset($data->os_version) ? filterData($data->os_version) : "";
$carrier = isset($data->carrier) ? filterData($data->carrier) : "";
$app_version = isset($data->app_version) ? filterData($data->app_version) : "";
$model = isset($data->model) ? filterData($data->model) : "";
$firebaseKey = isset($data->firebaseKey) ? filterData($data->firebaseKey) : "";

$query = mysqli_query($con, "SELECT * FROM devices_info WHERE app_member_id = '$uniqueID'");
if(mysqli_num_rows($query)){
    $sql = "UPDATE devices_info SET lang='$lang', type='$type', os_version='$os_version', carrier='$carrier', app_version='$app_version',
            model='$model', firebase_key='$firebaseKey' WHERE app_member_id = '$uniqueID'";
    if(mysqli_query($con, $sql)){
        $currentUserId = mysqli_insert_id($con);
        $response["Error"] = false;
        $response["Info"]["msg"] = "Device Details updated successfully";
    }else{
        $response["Error"] = true;
        $response["Info"]["msg"] = mysqli_error($con);
        echo json_encode($response); exit(); die();
    }
    $s = "SELECT * FROM devices_info WHERE app_member_id = '$uniqueID'";
    $r = mysqli_query($con, $s);
    $ro = mysqli_fetch_array($r);
    $response["deviceID"] = $ro["id"];
}else{
    $sql = "INSERT INTO devices_info (lang, type, os_version, carrier, app_version, model, app_member_id, firebase_key) VALUES 
            ('$lang', '$type', '$os_version', '$carrier', '$app_version', '$model', '$uniqueID', '$firebaseKey')";
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
}



echo json_encode($response);