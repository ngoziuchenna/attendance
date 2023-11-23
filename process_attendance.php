<?php

$allowedIPRange = '192.168.0.2/253'; // Replace with the actual IP range
$clientIP = $_SERVER['REMOTE_ADDR'];

if (!ip_in_range($clientIP, $allowedIPRange)) {
    http_response_code(403); // Forbidden
    die('Access forbidden');
}

header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);

// Connect to the MySQL database
$servername = "DESKTOP-KR2VJB1";
$username = "root";
$password = "Ucee1812!";
$dbname = "attendance1";                                                                                                            

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data 
    $userID = $_POST["userID"];
    $isPresent = $_POST["isPresent"];
    $attendanceDate = $_POST["attendanceDate"];

    // Insert data into the database
    $sql = "INSERT INTO attendance (userID, isPresent, attendanceDate) VALUES ('$userID', '$isPresent', '$attendanceDate')";

    if ($conn->query($sql) === TRUE) {
        echo "Attendance recorded successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// After defining $sql
echo "SQL Query: " . $sql;


// Close the database connection
$conn->close();

function ip_in_range($ip, $range) {
    list($subnet, $mask) = explode('/', $range);
    $subnet = ip2long($subnet);
    $ip = ip2long($ip);
    $mask = ~((1 << (32 - $mask)) - 1);

    return ($ip & $mask) == ($subnet & $mask);
}


?>
