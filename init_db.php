<?php
$host = 'localhost';
$user = 'root';
$pass = '';

// Create connection without database first
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read schema.sql
$sql = file_get_contents('schema.sql');

// Execute multi query
if ($conn->multi_query($sql)) {
    echo "Database created and tables initialized successfully.";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->close();
?>
