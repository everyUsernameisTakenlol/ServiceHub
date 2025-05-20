<?php
session_start();

// Establish database connection
$conn = new mysqli('localhost', 'root', '', 'home');

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize input
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Prepare the SQL query
    $sql = "SELECT * FROM providers WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);  // 's' means the email is a string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $provider = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $provider['password'])) {
            // Set session variables
            $_SESSION['username'] = $provider['name'];  // Store name as username
            $_SESSION['email'] = $provider['email'];
            $_SESSION['role'] = $provider['role'];  // Store the role in session

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            // Incorrect password
            $message = "Incorrect password!";
            header("Location: provider_login.php?message=" . urlencode($message));
            exit;
        }
    } else {
        // Email not found
        $message = "Email not found! Please sign up first.";
        header("Location: provider_login.php?message=" . urlencode($message));
        exit;
    }

}

$conn->close(); // Close database connection
?>
