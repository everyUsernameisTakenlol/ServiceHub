<?php
session_start(); // Start the session to access session variables

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit;
}

require_once '../db.php'; // Modify with your actual DB connection file

// Get the worker ID from the URL
if (!isset($_GET['worker_id']) || empty($_GET['worker_id'])) {
    echo "Worker ID is missing!";
    exit;
}
$worker_id = intval($_GET['worker_id']);

// Fetch the worker's assigned bookings with provider details, including both 'ongoing' and 'completed' statuses
$query = "SELECT 
            b.id, b.service_name, b.username, b.address, b.province, b.date, b.time, b.payment_method, b.status,
            p.name AS provider_name
          FROM bookings b
          LEFT JOIN providers AS p ON b.provider_id = p.id
          LEFT JOIN workers AS w ON b.worker_id = w.id
          WHERE w.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/table.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <style>
        button.complete-btn {
            display: inline-block;
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
        button.complete-btn:hover:not(:disabled) {
            background: #FF4B2B;
            color: white;
        }

        button.complete-btn:hover:disabled {
            background: #FF4B2B;
            color: white;
        }

        button.complete-btn:disabled {
            background-color: #D3D3D3; /* Grey background when disabled */
            cursor: not-allowed; /* Indicate that it's not clickable */
            border: none;
            color: gray;
        }

        .button.complete-btn {
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

        table {
    width: auto; /* Adjust the table width dynamically based on content */
    border-collapse: collapse; /* Clean border merging */
    margin: 0 auto; /* Center the table */
    margin-left: 5px; /* Shift the table to the right */
    table-layout: auto; /* Dynamic column widths */
}

th, td {
    padding: 8px 12px; /* Add space for cleaner look */
    text-align: left; /* Align text to the left */
    border: 1px solid #ddd; /* Light border for separation */
    white-space: nowrap; /* Prevent wrapping for short content */
}

th {
    background-color: #f4f4f4; /* Light background for headers */
    font-weight: bold;
}

td {
    white-space: nowrap; /* Prevent wrapping for short content */
}

/* Add zebra striping for better readability */
tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Highlight rows on hover */
tbody tr:hover {
    background-color: #f1f1f1;
}



        

    </style>
    <script>
        function enableCompleteButton(rowId, dateTime, status) {
            const currentDateTime = new Date(); // Get the current date and time
            console.log(`Current DateTime: ${currentDateTime}`);

            // Parse the assigned date and time in ISO format
            const assignedDateTime = new Date(dateTime);
            console.log(`Assigned DateTime: ${assignedDateTime}`);

            // Enable the button only if the current date and time meet or exceed the assigned date and time, and the status is 'ongoing'
            const button = document.getElementById(`completeBtn-${rowId}`);
            if (status.trim() === 'Ongoing' && currentDateTime >= assignedDateTime) {
                button.disabled = false;
            } else {
                button.disabled = true;
            }
        }

        function initializeButtons() {
            const rows = document.querySelectorAll("tbody tr");
            rows.forEach(row => {
                const rowId = row.querySelector("td:first-child").innerText; // Booking ID
                const dateTime = row.querySelector("td:nth-child(6)").innerText + 'T' + row.querySelector("td:nth-child(7)").innerText; // Date + Time in ISO format
                const status = row.querySelector("td:nth-child(9)").innerText; // Status
                enableCompleteButton(rowId, dateTime, status); // Call enable logic
            });
        }

        function markAsComplete(bookingId, rowId) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "mark_complete.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    alert(xhr.responseText);
                    document.getElementById(`status-${rowId}`).innerText = "Completed"; // Update the status on the page
                    document.getElementById(`completeBtn-${rowId}`).disabled = true; // Disable the complete button after it's clicked
                }
            };
            xhr.send("booking_id=" + bookingId);
        }
    </script>
</head>
<body onload="initializeButtons();">

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
            <h4><li><a href="workers.php"> <i class="fa-solid fa-users"></i> &nbsp; Workers </a></li></h4>
            <h4><li><a href="bookings.php"> <i class="fa-solid fa-book-open"></i> &nbsp; Bookings </a></li></h4>
            <h4><li><a href="history.php"> <i class="fa-solid fa-clock-rotate-left"></i> &nbsp; History </a></li></h4>
            <h4><li><a href="../index.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>
        </ul>
    </aside>

<div class="main-content">
    <h3 align="center">Assigned Service</h3>
    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Service Name</th>
                <th>Customer Username</th>
                <th>Address</th>
                <th>Province</th>
                <th>Date</th>
                <th>Time</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Provider Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $rowId = $row['id'];
                    $dateTime = $row['date'] . 'T' . $row['time'];
                    $status = $row['status'];
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['service_name']}</td>
                        <td>{$row['username']}</td>
                        <td>{$row['address']}</td>
                        <td>{$row['province']}</td>
                        <td>{$row['date']}</td>
                        <td>{$row['time']}</td>
                        <td>{$row['payment_method']}</td>
                        <td id='status-$rowId'>{$row['status']}</td>
                        <td>{$row['provider_name']}</td>
                        <td>
                            <button class='complete-btn' id='completeBtn-$rowId' onclick='markAsComplete($rowId, $rowId)' disabled>Complete</button>
                        </td>
                    </tr>
                    <script>
                        enableCompleteButton($rowId, '$dateTime', '{$row['status']}');
                    </script>";
                }
            } else {
                echo "<tr><td colspan='11'>No bookings found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>

<a href="workers.php" class="btn">Back to Workers List</a>

</body>
</html>
