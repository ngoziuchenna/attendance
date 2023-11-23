<?php

$allowedIPRange = '192.168.0.1/250'; // Replace with the actual IP range
$clientIP = $_SERVER['REMOTE_ADDR'];

if (!ip_in_range($clientIP, $allowedIPRange)) {
    http_response_code(403); // Forbidden
    die('Access forbidden');
}

//CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

//Forward the request to backend
$backendUrl = "http://localhost:3000/process_attendance.php";
echo
file_get_contents($backendUrl);

function ip_in_range($ip, $range) {
    list($subnet, $mask) = explode('/', $range);
    $subnet = ip2long($subnet);
    $ip = ip2long($ip);
    $mask = ~((1 << (32 - $mask)) - 1);

    return ($ip & $mask) == ($subnet & $mask);
}