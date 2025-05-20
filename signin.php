<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'home');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check if the email exists
    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            header("Location: user/dashboard.php");
            exit;
        } else {
            // Redirect with error message
            $message = "Incorrect password!";
            header("Location: login.php?message=" . urlencode($message));
            exit;
        }
    } else {
        // Redirect with error message
        $message = "Email not found! Please sign up first.";
        header("Location: login.php?message=" . urlencode($message));
        exit;
    }
}

$conn->close();
?>
