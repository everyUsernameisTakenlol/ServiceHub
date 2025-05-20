<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch notifications for completed bookings including worker roles
$notification_query = "
    SELECT b.id, b.service_name, w.worker_name, w.role 
    FROM bookings AS b
    INNER JOIN workers AS w ON b.worker_id = w.id
    WHERE b.username = '{$_SESSION['username']}' 
    AND b.status = 'Completed' 
    AND b.user_notified = 0";
$result_notifications = $conn->query($notification_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
<?php if ($result_notifications->num_rows > 0): ?>
    <?php 
    while ($notification = $result_notifications->fetch_assoc()) {
        // Extract worker and role details from the current row
        $worker_name = $notification['worker_name'] ?: 'N/A';
        $worker_role = $notification['role'] ?: 'N/A'; // Replace with 'N/A' if role is missing
        ?>
        <script>
            alert("You have a new completed booking. Check the details in history!\nYour Worker is: **<?php echo $worker_name; ?>**, Role: **<?php echo $worker_role; ?>**");
        </script>
    <?php 
    }
    ?>
    <?php 
    // Mark notifications as seen
    $update_notify_query = "
        UPDATE bookings 
        SET user_notified = 1 
        WHERE username = '{$_SESSION['username']}' 
        AND status = 'Completed'";
    $conn->query($update_notify_query);
    ?>
<?php endif; ?>
<header>
    <img src="../img/asd.png" alt="Logo" width="55" height="55">
    <a href="dashboard.php"><h2>ServiceHub  </h2></a>
    <div class="search-bar">
        <!-- <input type="text" placeholder="Search services..."> -->
    </div>
    <div class="profile">
        <h4><i class="fa-regular fa-user"></i> &nbsp; <?php echo $_SESSION['username']; ?></h4>
    </div>
</header>

<div class="dashboard-container">
    <aside class="sidebar">
        <ul>
            <h4><li><a href="browse.php"> <i class="fa-solid fa-magnifying-glass"></i> &nbsp; Services</a></li></h4>
            <h4><li><a href="requests.php"> <i class="fa-solid fa-receipt"></i> &nbsp; Requests</a></li></h4>
            <h4><li><a href="history.php"> <i class="fa-solid fa-clock-rotate-left"></i> &nbsp; History </a></li></h4>
            <h4><li><a href="address.php"> <i class="fa-solid fa-house"></i> &nbsp; Address</a></li></h4>
            <h4><li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>

        </ul>
    </aside>

    <main class="main-content">
        <section class="overview">
            <div class="hero-banner">
                <h1>Welcome to ServiceHub!</h1>
                <p>Your trusted platform for professional home services.</p>
            </div>

            <h2 class="section-title">Top Services</h2>
            <div class="top-services">
                <div class="service-card">
                    <img src="../img/cleaning.png" alt="Cleaning Service">
                    <h3>Cleaning</h3>
                    <p>Professional cleaning services for homes and offices.</p>
                </div>
                <div class="service-card">
                    <img src="../img/tap.png" alt="Plumbing Service">
                    <h3>Plumbing</h3>
                    <p>Expert solutions for all your plumbing needs.</p>
                </div>
                <div class="service-card">
                    <img src="../img/paint.png" alt="Painting Service">
                    <h3>Painting</h3>
                    <p>Transform your space with professional painting services.</p>
                </div>
            </div>
        </section>
    </main>
</div>

<script src="script.js"></script>
</body>
</html>
