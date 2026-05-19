<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "resort_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to match your SQL dump
$conn->set_charset("utf8mb4");
?>