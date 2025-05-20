<?php
// Start the session to store user choice
session_start();

// If a role is selected, store it in the session
if (isset($_POST['role'])) {
    $_SESSION['role'] = $_POST['role'];
    // Redirect based on the selected role
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/admin_login.php");
    } elseif ($_SESSION['role'] == 'customer') {
        header("Location: login.php");
    } elseif ($_SESSION['role'] == 'provider') {
        header("Location: provider/provider_login.php");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceHub</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="selection-container">
        <h1> <img src="img/logoo.png" alt="Logo" width="55" height="55">
        ServiceHub</h1>
        <form action="index.php" method="POST">
            <div class="role-option">
                <button type="submit" name="role" value="admin" class="book-now">Admin</button>
            </div>
            <div class="role-option">
                <button type="submit" name="role" value="customer" class="book-now">User</button>
            </div>
            <div class="role-option">
                <button type="submit" name="role" value="provider" class="book-now">Provider</button>
            </div>
        </form>
    </div>
</body>
</html>
