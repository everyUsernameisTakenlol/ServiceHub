<?php
session_start(); // Start the session to access session variables

// Ensure the user is logged in, redirect if not
if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit;
}

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
            <h4><li><a href="services.php"> <i class="fa-solid fa-briefcase"></i> &nbsp;  Services </a></li></h4>
            <h4><li><a href="bookings.php"> <i class="fa-solid fa-book-open"></i> &nbsp;  Bookings </a></li></h4>
            <h4><li><a href="../index.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>
        </ul>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <section class="overview">
            
        </section>
    </main>
</div>

<script src="script.js"></script>
</body>
</html>
