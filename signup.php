<?php
$conn = new mysqli('localhost', 'root', '', 'home');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";
$alertType = ""; // Alert type: success or error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $location = $conn->real_escape_string($_POST['location']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validate phone number contains only numbers
    if (!preg_match('/^\d+$/', $phone)) {
        $message = "Invalid credentials: Phone number must contain only numbers.";
        $alertType = "error";
    } else {
        // Check if the email already exists
        $checkEmail = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            $message = "Email already registered!";
            $alertType = "error";
        } else {
            // Insert the new user into the database
            $sql = "INSERT INTO users (name, phone, location, email, password) 
                    VALUES ('$name', '$phone', '$location', '$email', '$password')";

            if ($conn->query($sql) === TRUE) {
                $message = "Customer signup is successful!!";
                $alertType = "success";
            } else {
                $message = "Error: " . $conn->error;
                $alertType = "error";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Result</title>
    <script>
        // Function to display an alert and redirect based on the type
        function showAlert(message, type) {
            if (message) {
                alert(message);
                if (type === 'success') {
                    window.location.href = 'login.php'; // Redirect to login on success
                } else {
                    window.history.back(); // Go back to the previous form on error
                }
            }
        }
    </script>
</head>
<body>
    <!-- Call the showAlert function with PHP-generated message and type -->
    <script>
        const message = <?php echo json_encode($message); ?>;
        const alertType = <?php echo json_encode($alertType); ?>;
        showAlert(message, alertType);
    </script>
</body>
</html>
