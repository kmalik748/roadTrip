<?php
require 'modules/apiCore.php';

$data = json_decode(file_get_contents("php://input"));

$uniqueID = isset($data->uniqueID) ? filterData($data->uniqueID) : "";
$lat = isset($data->lat) ? filterData($data->lat) : "";
$long = isset($data->long) ? filterData($data->long) : "";
$radius = (int) isset($data->radius) ? filterData($data->radius) : "";
$firebaseKey = isset($data->firebaseKey) ? filterData($data->firebaseKey) : "";
$categories = isset($data->categories ) ? filterData($data->categories ) : "";
$currentUserId = null;

$sql = "SELECT * FROM devices_info WHERE id =$uniqueID LIMIT 1";
$result = mysqli_query($con, $sql);
if(mysqli_num_rows($result)){ //If id already exists do nothing
    $res = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($res);
    $currentUserId = $row["id"];
}else{ // If its not found
    $response["Error"] = true;
    $response["Info"]["msg"] = "Device is not registered!";
    echo json_encode($response); exit(); die();
}
$response["userID"] = $currentUserId;

$sql = "UPDATE devices_info SET firebase_key='$firebaseKey' WHERE id=$currentUserId";
mysqli_query($con, $sql);

// check for the nearest locations

$sql = "SELECT id, title, latitude, longitude, marker_radius, amazon_polly_audio_link_location as audio, opening_sound, closing_sound, categories, country, image, region, story_type, eventdate,
       ( 3959 * acos( cos( radians($lat) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians($long) ) + sin( radians($lat) ) * sin(radians(latitude)) ) ) AS distance
        FROM wp_audio_info WHERE categories = '$categories' HAVING distance < $radius ORDER BY distance LIMIT 0 , 20";
//echo $sql;
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