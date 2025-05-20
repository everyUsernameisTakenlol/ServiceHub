<?php
session_start(); // Start the session to access session variables

$servername = "localhost";
$username = "root";      
$password = "";      
$dbname = "home";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to login if the user is not logged in
    header("Location: login.php");
    exit();
}

$user = $_SESSION['username']; // Get the logged-in user's username

// Fetch the bookings for the logged-in user with status 'Pending' or 'Assigned'
// Fetch the bookings for the logged-in user with status 'Pending' or 'Assigned' or 'Ongoing'
$sql = "SELECT 
            b.id, 
            b.service_name, 
            b.address, 
            b.province, 
            b.date, 
            b.time, 
            b.payment_method, 
            b.status, 
            w.worker_name 
        FROM bookings b
        LEFT JOIN workers w ON b.worker_id = w.id
        WHERE b.username = ? AND b.status IN ('Pending', 'Assigned', 'Ongoing')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();



if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM bookings WHERE id = ? AND username = ?"; // Ensure only the user's own requests can be deleted
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("is", $delete_id, $user);
    $delete_stmt->execute();
    header("Location: requests.php"); 
    exit();
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

        /* Set specific column widths */
        th:nth-child(1), td:nth-child(1) { width: 6%; } /* Service Name column */
        th:nth-child(2), td:nth-child(2) { width: 20%; } /* Address column */
        th:nth-child(3), td:nth-child(3) { width: 20%; } /* Date column */
        th:nth-child(4), td:nth-child(4) { width: 14%; } /* Time column */
        th:nth-child(5), td:nth-child(5) { width: 13%; } /* Payment Method column */
        th:nth-child(6), td:nth-child(6) { width: 13%; } /* Status column */
        th:nth-child(7), td:nth-child(7) { width: 15%; } /* Action column */
        th:nth-child(8), td:nth-child(8) { width: 17%; } /* Action column */
        th:nth-child(9), td:nth-child(9) { width: 17%; } /* Action column */
        th:nth-child(10), td:nth-child(10) { width: 17%; } /* Action column */


        .delete-btn {
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

        .delete-btn:hover {
            background: #FF4B2B;
            color: white;
        }

        .edit-btn {
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

        .edit-btn:hover {
            background: #FF4B2B;
            color: white;
        }

        .disabled {
            background-color: #d3d3d3; /* Light gray */
            color: #888888; /* Dark gray text */
            cursor: not-allowed; /* Disabled cursor */
            pointer-events: none;
            border: 2px solid #d3d3d3;
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
    <aside class="sidebar">
        <ul>
            <h4><li><a href="browse.php"> <i class="fa-solid fa-magnifying-glass"></i> &nbsp; Services</a></li></h4>
            <h4><li><a href="requests.php"> <i class="fa-solid fa-receipt"></i> &nbsp;  Requests</a></li></h4>
            <h4><li><a href="history.php"> <i class="fa-solid fa-clock-rotate-left"></i> &nbsp; History </a></li></h4>
            <h4><li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>
        </ul>
    </aside>

    <main class="main-content">
        <section class="overview">
            <h2 class="section-title">Service Requests</h2>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Service Name</th>
                            <th>Address</th>
                            <th>Province</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Worker Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['address']); ?></td>
                                <td><?php echo htmlspecialchars($row['province']); ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><?php echo htmlspecialchars($row['time']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['worker_name'] ?? 'Not Assigned'); ?></td>
                                <td>
                                    <?php if ($row['status'] === "Ongoing"): ?>
                                        <a class="edit-btn disabled" href="#" style="cursor: not-allowed; color: gray;" onclick="return false;">Edit</a>
                                        <br>
                                        <a class="delete-btn disabled" href="#" style="cursor: not-allowed; color: gray;" onclick="return false;">Cancel</a>
                                    <?php else: ?>
                                        <a class="edit-btn" 
                                        data-id="<?php echo $row['id']; ?>" 
                                        data-service-name="<?php echo $row['service_name']; ?>" 
                                        data-address="<?php echo $row['address']; ?>" 
                                        data-province="<?php echo $row['province']; ?>" 
                                        data-date="<?php echo $row['date']; ?>" 
                                        data-time="<?php echo $row['time']; ?>" 
                                        data-payment-method="<?php echo $row['payment_method']; ?>"
                                        onclick="openModal(<?php echo $row['id']; ?>)">
                                        Edit
                                        </a>
                                        <br>
                                        <a href="requests.php?delete_id=<?php echo $row['id']; ?>" 
                                        class="delete-btn" 
                                        onclick="return confirm('Are you sure you want to cancel this request?');">
                                        Cancel
                                        </a>
                                    <?php endif; ?>
                                </td>







                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p align="center">No service requests found.</p>
            <?php endif; ?>
        </section>
    </main>
</div>


<div id="editModal" class="modal">
    
    <div class="modal-content">
        
        <span class="close-btn">&times;</span>
        <h2>Edit Booking</h2>
        <form id="editForm" action="edit_booking.php" method="POST">
            <input type="hidden" id="editId" name="id">

            <label for="editServiceName">Service Name:</label>
            <input type="text" id="editServiceName" name="service_name" required>

            <label for="editAddress">House No., Barangay, City:</label>
            <input type="text" id="editAddress" name="address" required>

            <label for="editProvince">Province, Postal Code:</label>
            <input type="text" id="editProvince" name="province" required>

            <label for="editDate">Date:</label>
            <input type="date" id="editDate" name="date" required>

            <label for="editTime">Time:</label>
            <input type="time" id="editTime" name="time" required>

            <label for="editPaymentMethod">Payment Method:</label>
            <select id="editPaymentMethod" name="payment_method" required>
                <option value="Cash">Cash</option>
                <option value="Card">Card</option>
                <option value="Online">Online</option>
            </select>

            <button type="submit" class="submit-btn">Save Changes</button>
        </form>
    </div>
</div>




<script>
    document.addEventListener("DOMContentLoaded", () => {
    const editModal = document.getElementById("editModal");
    const closeBtn = editModal.querySelector(".close-btn");
    const editBtns = document.querySelectorAll(".edit-btn");
    const editForm = document.getElementById("editForm");

    const editId = document.getElementById("editId");
    const editServiceName = document.getElementById("editServiceName");
    const editAddress = document.getElementById("editAddress");
    const editProvince = document.getElementById("editProvince");
    const editDate = document.getElementById("editDate");
    const editTime = document.getElementById("editTime");
    const editPaymentMethod = document.getElementById("editPaymentMethod");


    // Function to check if the time is within the disabled range (5:00 PM to 8:00 AM)
    function isTimeDisabled(time) {
        const [hours, minutes] = time.split(":").map(Number);
        const timeInMinutes = hours * 60 + minutes;
        const startDisabledTime = 17 * 60; // 5:00 PM in minutes
        const endDisabledTime = 8 * 60; // 8:00 AM in minutes

        return timeInMinutes >= startDisabledTime || timeInMinutes < endDisabledTime;
    }

    // Function to check if the selected date is in the past
    function isDateInThePast(date) {
        const today = new Date();
        const selectedDate = new Date(date);
        return selectedDate < today;
    }

    // Function to check if the selected date and time are in the future or present
    function isDateTimeValid(date, time) {
        const today = new Date();
        const selectedDateTime = new Date(date);
        const [hours, minutes] = time.split(":").map(Number);
        selectedDateTime.setHours(hours, minutes, 0, 0); // Set selected time on the selected date

        // Compare the selected date and time with the current date and time
        return selectedDateTime >= today;
    }


    // Open Edit Modal
    
    editBtns.forEach((btn) => {
        
        btn.addEventListener("click", (e) => {
            editId.value = e.target.getAttribute("data-id");
            editServiceName.value = e.target.getAttribute("data-service-name");
            editAddress.value = e.target.getAttribute("data-address");
            editProvince.value = e.target.getAttribute("data-province");
            editDate.value = e.target.getAttribute("data-date");
            editTime.value = e.target.getAttribute("data-time");
            editPaymentMethod.value = e.target.getAttribute("data-payment-method");

            editModal.style.display = "flex";
        });
    });

    // Close Edit Modal
    closeBtn.addEventListener("click", () => {
        editModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === editModal) {
            editModal.style.display = "none";
        }
    });

    // Disable times outside of 8:00 AM - 5:00 PM
editTime.addEventListener("input", () => {
    const selectedTime = editTime.value;
    if (isTimeDisabled(selectedTime)) {
        alert("This time is not available for booking. Please select a time between 8:00 AM and 5:00 PM.");
        editTime.value = "";
    }
});

// Validate the date and time on form submit
editForm.addEventListener("submit", (e) => {
    const selectedDate = editDate.value;
    const selectedTime = editTime.value;

    if (isDateInThePast(selectedDate)) {
        e.preventDefault();
        alert("This date is in the past. Please select today or a future date.");
    } else if (!isDateTimeValid(selectedDate, selectedTime)) {
        e.preventDefault();
        alert("This date and time are in the past. Please select a valid future time between 8:00 AM and 5:00 PM.");
    } else if (isTimeDisabled(selectedTime)) {
        e.preventDefault();
        alert("This time is not available. Please select between 8:00 AM and 5:00 PM.");
    }
});

});
</script>
</body>
</html>

<?php
$conn->close();
?>
