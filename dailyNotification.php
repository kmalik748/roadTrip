<?php
require 'modules/apiCore.php';

//$data = json_decode(file_get_contents("php://input"));

$GLOBALS["response_final"] = array(
    "Error" => false
);

$timestamp = date("d-m-Y");

$explodedDate = explode("-",$timestamp);
$day = $explodedDate[0];
$month = $explodedDate[1];

$sql = "SELECT * FROM wp_audio_info WHERE DAY(eventdate) = $day MONTH(eventdate) = $month order by id desc limit 1";
$sql = "SELECT * FROM wp_audio_info order by id desc limit 1";

$result = mysqli_query($con, $sql);
if(mysqli_num_rows($result)){
    $res = mysqli_fetch_array($result, MYSQLI_ASSOC);
//    $res = json_encode($res);
//    echo $res; exit(); die();
    send_notification($con, $res);
}else{
    $GLOBALS["response_final"]["Error"] = false;
    $GLOBALS["response_final"]["Body"]["msg"] = "No new data available.";
}

echo json_encode($GLOBALS["response_final"]);


function send_notification($con, $message){
    $sql = "SELECT * from devices_info";
    $query = mysqli_query($con, $sql);
    $tokens = array();
    while($row = mysqli_fetch_array($query)){
        $tokens[] = $row["firebase_key"];
    }

//    $tokens = json_encode($tokens);

    $url = 'https://fcm.googleapis.com/fcm/send';

    $server_key = "AAAA1DYKJLE:APA91bG58qqmfMe60znRQpB-13v5ZpFGZrEmi-4CMzJbuEY9IqTbf8sNsghhhWJZLVhX5mKm3Sx_C8UZYFc5KgN0KpQMyqeZ0sRPlop8hztZauTy-cwIdpWD03_P_LxNcd241P1kDh7G";
    $title = "Notification title";
    $payload = [
//        'to' => $tokens,
        'registration_ids' => $tokens,
        'notification' => [
            'title' => "Notification form server",
            'body' => "Message Here"
        ],
    "data" => $message
    ];

    $GLOBALS["response_final"]["payload"] = $payload;
    $fields = $payload;
    $headers = array(
        'Authorization:key = ' . $server_key,
        'Content-Type: application/json'
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    $result = curl_exec($ch);

    $GLOBALS["response_final"]["result"] = $result;
    if ($result === FALSE) {
        print_r($result);
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
}