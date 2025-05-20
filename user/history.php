<?php
session_start(); // Start the session to access session variables

// Ensure the user is logged in, redirect if not
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
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

// Fetch bookings with status "Completed"
$query = "SELECT 
            b.id, b.service_name, b.username, b.address, b.province, b.date, b.time, b.payment_method, b.status,
            p.name AS provider_name,
            w.worker_name AS worker_name
          FROM bookings AS b
          LEFT JOIN providers AS p ON b.provider_id = p.id
          LEFT JOIN workers AS w ON b.worker_id = w.id
          WHERE b.status = 'Completed' AND b.username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $_SESSION['username']);
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
    <link rel="stylesheet" href="../css/dashboard.css">

    <style>
        table {
            width: 100%;
            max-width: none;
            border-collapse: collapse;
            font-size: 16px;
            table-layout: fixed;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            font-size: 16px;
        }


        h3 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        th:nth-child(1), td:nth-child(1) { width: 6%; }   /* ID column */
        th:nth-child(2), td:nth-child(2) { width: 13%; }  /* Service Name column */
        th:nth-child(3), td:nth-child(3) { width: 15%; }  /* Username column */
        th:nth-child(4), td:nth-child(4) { width: 20%; }  /* Address column */
        th:nth-child(5), td:nth-child(5) { width: 18%; }  /* Province column */
        th:nth-child(6), td:nth-child(6) { width: 10%; }  /* Date column */
        th:nth-child(7), td:nth-child(7) { width: 10%; }  /* Time column */
        th:nth-child(8), td:nth-child(8) { width: 14%; }  /* Payment Method column */ 
        th:nth-child(9), td:nth-child(9) { width: 15%; }  /* Provider column */
        th:nth-child(10), td:nth-child(10) { width: 12%; }  /* Worker column */
        th:nth-child(11), td:nth-child(11) { width: 15%; }  /* Status column */
        th:nth-child(12), td:nth-child(12) { width: 16%; } /* Action column */


        .assign-btn {
    display: inline-block; /* Make the button behave like an inline element */
    text-decoration: none;
    font-weight: bold;
    color: #FF4B2B;
    border: 2px solid #FF4B2B;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: background 0.3s ease, color 0.3s ease;
    cursor: pointer;
    text-align: center;
}

        .assign-btn:hover {
            background: #FF4B2B;
            color: white;
        }
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
            <h4><li><a href="browse.php"> <i class="fa-solid fa-magnifying-glass"></i> &nbsp; Services</a></li></h4>
            <h4><li><a href="requests.php"> <i class="fa-solid fa-receipt"></i> &nbsp;  Requests</a></li></h4>
            <h4><li><a href="history.php"> <i class="fa-solid fa-clock-rotate-left"></i> &nbsp; History </a></li></h4>
            <h4><li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>
        </ul>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <section class="overview">
            <h2 align="center">Bookings History</h2><br>
            <?php if ($result->num_rows > 0): ?>
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
                            <td><?php echo date("Y-m-d", strtotime($row['date'])); ?></td>
                            <td><?php echo date("g:i A", strtotime($row['time'])); ?></td>
                            <td><?php echo $row['payment_method']; ?></td>
                            <td><?php echo $row['provider_name'] ?: 'N/A'; ?></td>
                            <td><?php echo $row['worker_name'] ?: 'N/A'; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>

                </table>
            <?php else: ?>
                <p align="center">No completed bookings found.</p>
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
