<?php
session_start(); // Start the session to access session variables

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = ''; // Initialize success message variable

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $service_name = $_POST['service_name'];
    $address = $_POST['address'];
    $province = $_POST['province']; // Fetch the province field
    $date = $_POST['date'];
    $time = $_POST['time']; // Time input in 24-hour format
    $payment_method = $_POST['payment_method'];
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; // Get the username from the session

    // Check if the logged-in user has already requested this address
    $check_sql = "SELECT * FROM bookings WHERE address = ? AND username = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $address, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Address already exists for the logged-in user, show an error message and redirect
        $_SESSION['error_message'] = "You have already submitted a request with this address.";
        header("Location: browse.php"); // Redirect immediately
        exit(); // Stop further script execution
    } else {
        // Convert the 24-hour time format to 12-hour format with AM/PM
        $formatted_time = date("g:i a", strtotime($time)); // "g:i a" gives "2:00 pm" format

        // Insert booking into the database
        $sql = "INSERT INTO bookings (service_name, address, province, date, time, payment_method, username) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $service_name, $address, $province, $date, $formatted_time, $payment_method, $username);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Booking successful!"; // Set success message in session
            header("Location: browse.php"); // Redirect immediately after successful booking
            exit(); // Stop further script execution
        } else {
            // Show an error message and redirect
            $_SESSION['error_message'] = "Error: " . $stmt->error;
            header("Location: browse.php"); // Redirect immediately
            exit(); // Stop further script execution
        }
    }

    $stmt->close();
}

$conn->close();
?>
