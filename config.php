<?php
// Database Configuration File
$servername = "localhost";
$username = "root"; // Default XAMPP user
$password = ""; // Default XAMPP password (usually blank)
$dbname = "alumni_management"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
