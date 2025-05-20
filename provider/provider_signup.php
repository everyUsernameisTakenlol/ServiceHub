<?php
$conn = new mysqli('localhost', 'root', '', 'home');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = ""; // Message to store the result
$alertType = ""; // Alert type (success or error)

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
        $checkEmail = "SELECT * FROM providers WHERE email='$email'";
        $result = $conn->query($checkEmail);

        if ($result->num_rows > 0) {
            $message = "Email already registered!";
            $alertType = "error";
        } else {
            // Insert the new provider into the database
            $sql = "INSERT INTO providers (name, phone, location, email, password) 
                    VALUES ('$name', '$phone', '$location', '$email', '$password')";

            if ($conn->query($sql) === TRUE) {
                $message = "Provider signup is successful!";
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
    <title>Provider Signup</title>
    <script>
        // Function to display an alert based on the message and alert type
        function showAlert(message, type) {
            if (message && type) {
                alert(message);
                if (type === 'success') {
                    window.location.href = 'provider_login.php'; // Redirect to login on success
                }
            }
        }
    </script>
</head>
<body>
    <!-- Call the showAlert function with PHP values -->
    <script>
        const message = <?php echo json_encode($message); ?>;
        const alertType = <?php echo json_encode($alertType); ?>;
        showAlert(message, alertType);
    </script>
</body>
</html>
