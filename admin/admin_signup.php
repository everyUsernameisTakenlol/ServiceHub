<?php
$conn = new mysqli('localhost', 'root', '', 'home');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    // Check if the email already exists in the admin table
    $checkEmail = "SELECT * FROM admin WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        // Insert new admin user
        $sql = "INSERT INTO admin (name, phone, email, password) 
                VALUES ('$name', '$phone', '$email', '$password')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful!";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$conn->close();

// Redirect back to login.php with a message parameter
header("Location: login.php?message=" . urlencode($message));
exit();
?>
