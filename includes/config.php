<?php

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "sewing"; 

define('BASE_URL', 'http://localhost/Final/');

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>