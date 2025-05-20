<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all providers
$query = "SELECT id, name FROM providers";
$result = $conn->query($query);
$providers = [];

while ($row = $result->fetch_assoc()) {
    $providers[] = $row;
}

echo json_encode($providers);
$conn->close();
?>
