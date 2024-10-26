<?php
// config.php

$host = "localhost";  // Database host
$db = "svalberg_motell"; // Database name
$user = "svalberg_user"; // Database username
$pass = "password"; // Database password

// Create connection
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
