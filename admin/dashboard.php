<?php
session_start(); // Start the session to access session variables

// Ensure the user is logged in, redirect if not
if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit;
}

// Prevent browser from caching the page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache"); 

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

<header>
    <img src="../img/asd.png" alt="Logo" width="55" height="55">
    <a href="dashboard.php"><h2>ServiceHub</h2></a>
    <div class="search-bar">
        <!-- <input type="text" placeholder="Search services..."> -->
    </div>
    <div class="profile">
        <!-- Display the username from the session -->
        <h4><i class="fa-regular fa-user"></i> &nbsp; <?php echo $_SESSION['username']; ?></h4>
    </div>
</header>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <ul>
            <h4><li><a href="users.php"> <i class="fa-solid fa-user"></i> &nbsp; Users </a></li></h4>
            <h4><li><a href="providers.php"> <i class="fa-solid fa-user-tie"></i> &nbsp;  Providers </a></li></h4>
            <!-- <h4><li><a href="services.php"> <i class="fa-solid fa-briefcase"></i> &nbsp;  Services </a></li></h4> -->
            <h4><li><a href="bookings.php"> <i class="fa-solid fa-book-open"></i> &nbsp;  Bookings </a></li></h4>
            <h4><li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>
        </ul>
    </aside>

    <!-- Main content -->
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
