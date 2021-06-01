<?php
require 'func.php';
require 'db.php';

// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$response = array(
    "Error" => false
);