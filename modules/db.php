<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_roadtrip";

$servername = "localhost";
$username = "bxbnuxqkra";
$password = "4wzFy9nVKJ";
$dbname = "bxbnuxqkra";


$con = mysqli_connect($servername, $username, $password, $dbname);
if(!$con){
    echo "Connection to server not established!!!  ". $conn -> connect_error; die(); exit();
}
$GLOBALS["con"] = $con;

date_default_timezone_set("America/Adak");   // US