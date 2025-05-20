<?php
session_start(); // Start the session to access session variables

// Ensure the user is logged in, redirect if not
if (!isset($_SESSION['username'])) {
    header("Location: provider_login.php");
    exit;
}

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home";

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the logged-in provider's name from the session
$provider_name = $_SESSION['username']; // Assuming 'username' is the provider's name in session


$stmt = $conn->prepare("SELECT 
            b.id, b.service_name, b.username, b.address, b.province, b.date, b.time, b.payment_method, b.status,
            p.name AS provider_name,
            w.worker_name AS worker_name
          FROM bookings AS b
          LEFT JOIN providers AS p ON b.provider_id = p.id
          LEFT JOIN workers AS w ON b.worker_id = w.id
          WHERE b.status = 'Completed' AND p.name = ?");
$stmt->bind_param("s", $provider_name);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    

    <style>
        th:nth-child(1), td:nth-child(1) { width: 6%; }   /* ID column */
        th:nth-child(2), td:nth-child(2) { width: 13%; }  /* Service Name column */
        th:nth-child(3), td:nth-child(3) { width: 16%; }  /* Username column */
        th:nth-child(4), td:nth-child(4) { width: 20%; }  /* Address column */
        th:nth-child(5), td:nth-child(5) { width: 18%; }  /* Province column */
        th:nth-child(6), td:nth-child(6) { width: 10%; }  /* Date column */
        th:nth-child(7), td:nth-child(7) { width: 12%; }  /* Time column */
        th:nth-child(8), td:nth-child(8) { width: 14%; }  /* Payment Method column */
        th:nth-child(9), td:nth-child(9) { width: 13%; }  /* Provider column */ 
        th:nth-child(10), td:nth-child(10) { width: 13%; }  /* Worker column */
        th:nth-child(11), td:nth-child(11) { width: 15%; }  /* Status column */
        </style>
 


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
            <h4><li><a href="workers.php"> <i class="fa-solid fa-users"></i> &nbsp; Workers </a></li></h4>
            <h4><li><a href="bookings.php"> <i class="fa-solid fa-book-open"></i> &nbsp; Bookings </a></li></h4>
            <h4><li><a href="history.php"> <i class="fa-solid fa-clock-rotate-left"></i> &nbsp; History </a></li></h4>
            <h4><li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>
        </ul>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <section class="overview">
            <h2 align="center">Bookings History</h2><br>
            <?php if ($result->num_rows > 0): ?>
                <div class="table-container">
                <table>
                <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Username</th>
                            <th>Address</th>
                            <th>Province</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Payment Method</th>
                            <th>Provider</th>
                            <th>Worker</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['service_name']; ?></td>
                            <td><?php echo $row['username']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['province']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['time']; ?></td>
                            <td><?php echo $row['payment_method']; ?></td>
                            <td><?php echo $row['provider_name'] ?: 'N/A'; ?></td>
                            <td><?php echo $row['worker_name'] ?: 'N/A'; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>

                </table>
                    </div>
            <?php else: ?>
                <p>No completed bookings found.</p>
            <?php endif; ?>
        </section>
    </main>
</div>

<script src="script.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
