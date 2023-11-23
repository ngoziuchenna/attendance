<?php

$allowedIPRange = '192.168.0.2/253'; // Replace with the actual IP range
$clientIP = $_SERVER['REMOTE_ADDR'];

if (!ip_in_range($clientIP, $allowedIPRange)) {
    http_response_code(403); // Forbidden
    die('Access forbidden');
}

header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);

//connect to the MySQL database
$servername = "localhost";
$username = "root";
$password = "Ucee1812!";
$dbname = "attendance";

$conn = new mysqli($servername, $username, $password, $dbname);

//check connection
if ($conn->connect_error) {die("Connection failed: " . $conn->connect_error);
}

//process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data 
    $FirstName= $_POST["FirstName"];
    $LastName= $_POST["LastName"];
    $ID= $_POST["ID"];
    $RoleT = $_POST["RoleT"];
    $Project = $_POST["Project"];

//Insert data into the database
$sql = "INSERT INTO user (FirstName, ID, LastName, RoleT, Project) VALUES ('$FirstName', '$ID', $LastName', '$RoleT', '$Project)";

if ($conn->query($sql) === TRUE) {
    echo "User added successfully";
}
else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
}

//Close the database connection
$conn->close();

function ip_in_range($ip, $range) {
    list($subnet, $mask) = explode('/', $range);
    $subnet = ip2long($subnet);
    $ip = ip2long($ip);
    $mask = ~((1 << (32 - $mask)) - 1);

    return ($ip & $mask) == ($subnet & $mask);
}

?>