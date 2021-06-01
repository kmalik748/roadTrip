<?php
function filterData($parm){
    $parm = trim($parm);
    //$parm = mysqli_real_escape_string($parm);
    $parm = htmlentities($parm);
    $parm = strip_tags($parm);
    return $parm;
}

function locationWasSent($user, $location){
    require 'modules/timezone.php';
    $dateNow = date("Y-m-d");
    $sql = "INSERT INTO sent_requests (device_id, location_id, date_now) VALUES ($user, $location, '$dateNow')";
    return mysqli_query($GLOBALS["con"], $sql) ? true : mysqli_error($GLOBALS["con"]);
}