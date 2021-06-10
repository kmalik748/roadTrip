<?php
require 'modules/apiCore.php';

$data = json_decode(file_get_contents("php://input"));
$data = json_decode(json_encode($data), true);
$tags = "'".implode("','",$data)."'";


date_default_timezone_set("Asia/Calcutta");   //India time (GMT+5:30)
$timestamp = date("d-m-Y");

//$sql = "SELECT id, title, latitude, longitude, marker_radius, amazon_polly_audio_link_location as audio, opening_sound, closing_sound, categories, country, image, region, story_type, eventdate
//    FROM wp_audio_info WHERE story_type = '$story_type' AND eventdate='$timestamp' LIMIT 0 , 20";

$sql = "SELECT id, title, latitude, longitude, marker_radius, amazon_polly_audio_link_location as audio, opening_sound, closing_sound, categories, country, image, region, story_type, eventdate
    FROM wp_audio_info WHERE categories IN ($tags)  LIMIT 0 , 20";


$res = mysqli_query($con, $sql);
if(mysqli_num_rows($res)){
    $geoLocations = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_free_result($res);
    $response["geoLocations"] = $geoLocations;
}

echo json_encode($response);