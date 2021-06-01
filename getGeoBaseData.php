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
$lat = isset($data->lat) ? filterData($data->lat) : "";
$long = isset($data->long) ? filterData($data->long) : "";
$radius = isset($data->radius) ? filterData($data->radius) : "";
$currentUserId = null;

$sql = "SELECT * FROM devices_info WHERE id =$uniqueID LIMIT 1";
$result = mysqli_query($con, $sql);
if(mysqli_num_rows($result)){ //If id already exists do nothing
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($res);
    $currentUserId = $row["id"];
}else{ // If its new ID, insert data into DB
    $sql = "INSERT INTO devices_info (lang, type, os_version, carrier, app_version, model, app_member_id) VALUES 
            ('$lang', '$type', '$os_version', '$carrier', '$app_version', '$model', '$appMemberId')";
    if(mysqli_query($con, $sql)){
        $currentUserId = mysqli_insert_id($con);
        $response["Error"] = false;
        $response["Info"]["msg"] = "New device added successfully";
    }else{
        $response["Error"] = true;
        $response["Info"]["msg"] = mysqli_error($con);
        echo json_encode($response); exit(); die();
    }
}
$response["userID"] = $currentUserId;

// check for the nearest locations

$sql = "SELECT id, title, latitude, longitude, marker_radius, 
       ( 3959 * acos( cos( radians($lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($long) ) + sin( radians($lat) ) * sin(radians(latitude)) ) ) AS distance
        FROM wp_audio_info HAVING distance < 25 ORDER BY distance LIMIT 0 , 20";
$res = mysqli_query($con, $sql);
$newLocations = array();
if(mysqli_num_rows($res)){
     $geoLocations = mysqli_fetch_all($res, MYSQLI_ASSOC);
     mysqli_free_result($result);
     require 'modules/timezone.php';
     $dateNow = date("Y-m-d");
     $s = "SELECT * FROM sent_requests WHERE Date(date_now) = '$dateNow' AND device_id=$currentUserId";
     $r = mysqli_query($con, $s);
     $sentRequests = mysqli_fetch_all($r, MYSQLI_ASSOC);

     $sentArray = array();
     foreach ($sentRequests as $rqst){
         if(isset($rqst["id"])){
             array_push($sentArray, $rqst["location_id"]);
         }
     }
//     echo "SENT LOCATIONS: ";
//     print_r($sentArray);
     foreach ($geoLocations as $location){
//         echo "nwe locations: ".$location["id"];
        if(!in_array($location["id"], $sentArray)){
            array_push($newLocations, $location);
            locationWasSent($currentUserId, $location["id"]);
        }
     }


//    foreach ($geoLocations as $location){
//        print_r($sentRequests);
//     foreach ($sentRequests as $sentLocation){
//            echo $location["id"].'     ---      '.$sentLocation["location_id"].'<br>';
////            if($location["id"]!=$sentLocation["id"]){
////                array_push($newLocations, $location);
////                locationWasSent($currentUserId, $location["id"]);
////            }
//        }
//     }
     $response["geoLocations"] = $newLocations;
}


echo json_encode($response);