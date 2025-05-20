<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is provided
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    // Update the status to "Assigned"
    $updateQuery = "UPDATE bookings SET status = 'Assigned' WHERE id = $id";
    
    if ($conn->query($updateQuery) === TRUE) {
        echo "Booking successfully assigned!";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>
