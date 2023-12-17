<?php
/*$host = "localhost";
$dbname = "login_db";
$username ="root";
$password = "";

$mysqli = new mysqli(
    $host,
    $username, 
    $password, 
    $dbname
);

if ($mysqli->connect_error) {
    die("connectionerror: " . $mysqli->connect_error);
}
return $mysqli;
*/

$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "login_db"; 


$conn = mysqli_connect($servername, $username, $password, $dbname);


if (!$conn) {
    echo "Failed to connect: " . mysqli_connect_error();
    exit();
} else {
    //echo "Connected successfully";
}
?>
