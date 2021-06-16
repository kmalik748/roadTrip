<?php
require 'modules/apiCore.php';
$data = json_decode(file_get_contents("php://input"));

$timestamp = date("d-m-Y");
//echo $timestamp;
$explodedDate = explode("-",$timestamp);
$day = $explodedDate[0];
$month = $explodedDate[1];
$storyType ="HistoryToday";
$type="HistoryToday";
//$sql = "SELECT * FROM wp_audio_info WHERE story_type = $storyType AND DAY(eventdate) = $day AND MONTH(eventdate) = $month";
$sql = "SELECT id, title, latitude, longitude, marker_radius, amazon_polly_audio_link_location as audio, opening_sound, closing_sound, categories, country, image, region, story_type, eventdate
    FROM wp_audio_info WHERE story_type = '$type'  LIMIT 0 , 20";

$res = mysqli_query($con, $sql);
if(mysqli_num_rows($res)){
    $geoLocations = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_free_result($res);
    $response["geoLocations"] = array();
    foreach ($geoLocations as $location){
    	//echo "here";
        $location = array_map('utf8_encode', $location);
        array_push($response["geoLocations"], $location);
    }
}else{
    $response["Error"] = true;
    $response["msg"] = "No data for the today's date";
}

echo json_encode($response);