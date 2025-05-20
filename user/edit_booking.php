<?php
// Start the session to ensure session variables are accessible
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $service_name = $_POST['service_name'];
    $address = $_POST['address'];
    $province = $_POST['province'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $payment_method = $_POST['payment_method'];

    // Update query
    $sql = "UPDATE bookings 
            SET service_name = ?, address = ?, province = ?, date = ?, time = ?, payment_method = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssssssi", $service_name, $address, $province, $date, $time, $payment_method, $id);

        if ($stmt->execute()) {
            // Redirect back to the bookings page after successful update
            header("Location: requests.php");
            exit();
        } else {
            echo "Error updating booking: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
}

$conn->close();
?>
