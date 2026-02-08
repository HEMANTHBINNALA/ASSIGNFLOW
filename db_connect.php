<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password
$db_name = 'assignment_portal';

$conn = new mysqli($host, $user, $pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
