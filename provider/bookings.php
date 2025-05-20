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

// Handle the "Complete" button
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_id'])) {
    $complete_id = intval($_POST['complete_id']);
    $update_query = "UPDATE bookings SET status = 'Completed' WHERE id = $complete_id";
    if ($conn->query($update_query)) {
        echo "<script>alert('Booking has been Completed');</script>";
        header("Refresh:0"); // Refresh the page to show updated status
        exit;
    } else {
        echo "<script>alert('Failed to update status');</script>";
    }
}

// Handle the worker assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_worker']) && isset($_POST['booking_id']) && isset($_POST['worker_id'])) {
    $booking_id = intval($_POST['booking_id']);
    $worker_id = intval($_POST['worker_id']);
    
    // Fetch the date and time of the booking being updated
    $booking_query = "SELECT date, time FROM bookings WHERE id = $booking_id";
    $booking_result = $conn->query($booking_query);
    
    if ($booking_result->num_rows > 0) {
        $booking = $booking_result->fetch_assoc();
        $booking_date = $booking['date'];
        $booking_time = $booking['time'];
        
        // Check if the worker is already assigned to another booking at the same date and time
        $check_worker_query = "SELECT id FROM bookings WHERE worker_id = $worker_id AND date = '$booking_date' AND time = '$booking_time' AND status != 'Completed'";
        $check_worker_result = $conn->query($check_worker_query);
        
        if ($check_worker_result->num_rows > 0) {
            echo "<script>alert('The selected worker is already assigned to another booking at the same time. Please choose another worker.');</script>";
        } else {
            // Update the booking with the assigned worker and set the status to 'Ongoing'
            $update_query = "UPDATE bookings SET worker_id = $worker_id, status = 'Ongoing' WHERE id = $booking_id";
            if ($conn->query($update_query)) {
                echo "<script>alert('Worker assigned');</script>";
                header("Refresh:0");
                exit;
            } else {
                echo "<script>alert('Failed to assign worker');</script>";
            }
        }
    } else {
        echo "<script>alert('Booking not found');</script>";
    }
}


// Fetch bookings with status "Assigned" or "Ongoing", and include worker information
$query = "SELECT b.id, b.service_name, b.username, b.address, b.province, b.date, b.time, b.payment_method, b.status, 
          p.name AS provider_name, w.worker_name 
          FROM bookings b 
          LEFT JOIN providers p ON b.provider_id = p.id 
          LEFT JOIN workers w ON b.worker_id = w.id
          WHERE b.status = 'Assigned' AND p.name = '$provider_name'";
$result = $conn->query($query);


// Fetch workers for the logged-in provider
// Fetch workers for the logged-in provider based on the role
$workers_query = "SELECT id, worker_name FROM workers WHERE role = (SELECT role FROM providers WHERE name = '$provider_name')";
$workers_result = $conn->query($workers_query);

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

       


        h3 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        th:nth-child(1), td:nth-child(1) { width:6%; }   /* ID column */
        th:nth-child(2), td:nth-child(2) { width: 15%; }  /* Service Name column */
        th:nth-child(3), td:nth-child(3) { width: 17%; }  /* Username column */
        th:nth-child(4), td:nth-child(4) { width: 20%; }  /* Address column */
        th:nth-child(5), td:nth-child(5) { width: 16%; }     /* Province column */
        th:nth-child(6), td:nth-child(6) { width: 12%; }  /* Date column */
        th:nth-child(7), td:nth-child(7) { width: 12%; }  /* Time column */
        th:nth-child(8), td:nth-child(8) { width: 16%; }  /* Payment Method column */
        th:nth-child(9), td:nth-child(9) { width: 15%; }  /* Provider column */ 
        th:nth-child(10), td:nth-child(10) { width: 15%; }  /* Worker column */
        th:nth-child(11), td:nth-child(11) { width: 14%; }  /* Status column */
        th:nth-child(12), td:nth-child(12) { width: 20%; } /* Action column */


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
        /* Modal styles */
        .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        display: none; /* Initially hidden */
        justify-content: center;
        align-items: center; /* Centers the modal vertically and horizontally */
       
    }

    

    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        width: 60%;
        max-width: 400px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        position: relative;
        margin-top: 200px;
        margin-left: 500px;
    }


    .modal .close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        cursor: pointer;
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
            <!-- <h4><li><a href="users.php"> <i class="fa-solid fa-pen"></i> &nbsp; Availability </a></li></h4> -->
            <h4><li><a href="workers.php"> <i class="fa-solid fa-users"></i> &nbsp; Workers </a></li></h4>
            <h4><li><a href="bookings.php"> <i class="fa-solid fa-book-open"></i> &nbsp; Bookings </a></li></h4>
            <h4><li><a href="history.php"> <i class="fa-solid fa-clock-rotate-left"></i> &nbsp; History </a></li></h4>
            <h4><li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>
        </ul>
    </aside>

    <!-- Main content -->
    <main class="main-content">
        <section class="overview">
            <h2 align="center">Assigned Bookings</h2><br>
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
                        <th>Action</th>
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
                            <td><?php echo date("g:i A", strtotime($row['time'])); ?></td>
                            <td><?php echo $row['payment_method']; ?></td>
                            <td><?php echo $row['provider_name'] ?: 'Not Assigned'; ?></td>
                            <td><?php echo $row['worker_name'] ?: 'Not Assigned'; ?></td> <!-- Display assigned worker -->
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <?php if ($row['status'] == 'Ongoing'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="complete_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="assign-btn">Complete</button>
                                    </form>
                                <?php else: ?>
                                    <button class="assign-btn" onclick="openModal(<?php echo $row['id']; ?>)">Select Worker</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>


                </table>
            <?php else: ?>
                <p align="center">No assigned bookings found.</p>
            <?php endif; ?>
        </section>
    </main>
</div>

<!-- Modal for worker selection -->
<!-- Modal for worker selection -->
<div id="workerModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Select Worker</h3>
        <form method="POST">
            <input type="hidden" id="booking_id" name="booking_id">
            <label for="worker_id">Select Worker:</label>
            <select id="worker_id" name="worker_id">
                <?php while ($worker = $workers_result->fetch_assoc()): ?>
                    <option value="<?php echo $worker['id']; ?>"><?php echo $worker['worker_name']; ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit" name="assign_worker">Assign Worker</button>
        </form>
    </div>
</div>


<script>
    // Open the modal
    function openModal(bookingId) {
        document.getElementById("booking_id").value = bookingId;
        document.getElementById("workerModal").style.display = "block";
    }

    // Close the modal
    function closeModal() {
        document.getElementById("workerModal").style.display = "none";
    }

    // Close the modal if the user clicks anywhere outside of the modal
    window.onclick = function(event) {
        if (event.target == document.getElementById("workerModal")) {
            closeModal();
        }
    }
</script>

<script src="script.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
