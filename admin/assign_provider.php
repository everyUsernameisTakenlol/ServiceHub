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

// Handle POST data
$data = json_decode(file_get_contents("php://input"), true);
$bookingId = intval($data['bookingId'] ?? 0);
$providerId = intval($data['providerId'] ?? 0);

if ($bookingId > 0 && $providerId > 0) {
    $query = "UPDATE bookings SET provider_id = ?, status = 'Assigned' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $providerId, $bookingId);

    if ($stmt->execute()) {
        echo "Provider assigned successfully.";
    } else {
        echo "Error assigning provider: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Invalid booking or provider ID.";
}

$conn->close();
?>
