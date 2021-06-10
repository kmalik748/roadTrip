<?php
require 'modules/apiCore.php';
$data = json_decode(file_get_contents("php://input"));

$timestamp = date("d-m-Y");
$explodedDate = explode("-",$timestamp);
$day = $explodedDate[0];
$month = $explodedDate[1];
$sql = "SELECT * FROM wp_audio_info WHERE DAY(eventdate) = $day AND MONTH(eventdate) = $month";

$res = mysqli_query($con, $sql);
if(mysqli_num_rows($res)){
    $geoLocations = mysqli_fetch_all($res, MYSQLI_ASSOC);
    mysqli_free_result($res);
    $response["geoLocations"] = $geoLocations;
}else{
    $response["msg"] = "No data for the today's date";
}

echo json_encode($response);