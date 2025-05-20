<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'home');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check if the email exists in the admin table
    $sql = "SELECT * FROM admin WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['username'] = $admin['name'];  // Store the name instead of email
            $_SESSION['email'] = $admin['email'];

            header("Location: dashboard.php");
            exit;
        } else {
            // Redirect with error message
            $message = "Incorrect password!";
            header("Location: admin_login.php?message=" . urlencode($message));
            exit;
        }
    } else {
        // Redirect with error message
        $message = "Email not found!";
        header("Location: admin_login.php?message=" . urlencode($message));
        exit;
    }
}

$conn->close();
?>
