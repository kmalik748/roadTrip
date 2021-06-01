<?php
require 'modules/apiCore.php';

$data = json_decode(file_get_contents("php://input"));



$sql = "SELECT * FROM devices_info";
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