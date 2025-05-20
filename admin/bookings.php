<?php
session_start(); // Start the session to access session variables

// Ensure the user is logged in, redirect if not
if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit;
}

// Database connection details
$servername = "localhost"; // Replace with your server name
$username = "root";        // Replace with your database username
$password = "";            // Replace with your database password
$dbname = "home";    // Replace with your database name

// Create a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bookings data
$query = "SELECT id, service_name, username, address, province, date, time, payment_method, status FROM bookings";

$result = $conn->query($query);

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
    <link rel="stylesheet" href="../css/table.css">    
    <link rel="stylesheet" href="../css/dashboard.css">

    <style>

        h3 {
            font-size: 24px;
            margin-bottom: 20px;
        }


        .assign-btn {
            display: block;
            text-decoration: none;
            font-weight: bold;
            color: #FF4B2B;
            border: 2px solid #FF4B2B;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: background 0.3s ease, color 0.3s ease;
            text-align: center;
            cursor: pointer;
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
            <h2 align="center">Bookings</h2><br>
            <table border="1" class="bookings-table">
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
        <th>Status</th>
        <th>Action</th>
    </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
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
                        <td><?php echo $row['status']; ?></td>
                        <td>
                            <?php if ($row['status'] === "Assigned" || $row['status'] === "Completed" || $row['status'] === "Ongoing"): ?>
                                <button class="assign-btn disabled" disabled>Assign</button>
                            <?php else: ?>
                                <button class="assign-btn" onclick="openModal(<?php echo $row['id']; ?>)">Assign</button>
                            <?php endif; ?>
                        </td>


                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10">No bookings found.</td>
                </tr>
            <?php endif; ?>
        </tbody>

            </table>
        </section>
    </main>
</div>

<!-- Modal for selecting a provider -->
<div id="providerModal" class="modal" style="display: none;">
    <div class="modal-content">
        <form id="assignForm">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Select a Provider</h2>
            <select id="providerSelect">
                <option value="">Select a provider</option>
                <!-- Provider options will be dynamically inserted here -->
            </select>
            <input type="hidden" id="bookingId" name="bookingId">
            <br><br>
            <button type="button" onclick="assignProvider()">Assign</button>
        </form>
    </div>
</div>


<script>
    function openModal(bookingId) {
    document.getElementById("bookingId").value = bookingId;
    document.getElementById("providerModal").style.display = "block";

    // Fetch providers dynamically (replace with an actual API endpoint if needed)
    fetch("fetch_providers.php")
        .then(response => response.json())
        .then(data => {
            const providerSelect = document.getElementById("providerSelect");
            providerSelect.innerHTML = '<option value="">Select a provider</option>';
            data.forEach(provider => {
                const option = document.createElement("option");
                option.value = provider.id;
                option.textContent = provider.name;
                providerSelect.appendChild(option);
            });
        });
}

function closeModal() {
    document.getElementById("providerModal").style.display = "none";
}

function assignProvider() {
    const bookingId = document.getElementById("bookingId").value;
    const providerId = document.getElementById("providerSelect").value;

    if (!providerId) {
        alert("Please select a provider.");
        return;
    }

    // Send data to the server via AJAX
    fetch("assign_provider.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ bookingId, providerId }),
    })
        .then(response => response.text())
        .then(data => {
            alert(data);
            closeModal();
            location.reload(); // Reload the page to reflect updates
        })
        .catch(error => console.error("Error:", error));
}

</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
