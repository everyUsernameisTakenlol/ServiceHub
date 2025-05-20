<?php
session_start(); // Start the session to access session variables

// Ensure the user is logged in, redirect if not
if (!isset($_SESSION['username'])) {
    header("Location: provider_login.php");
    exit;
}

require_once '../db.php'; // Modify with your actual DB connection file

// Get the logged-in user's role from the session
$user_role = $_SESSION['role'];

// Fetch workers from the database where the role matches the logged-in user's role
$query = "SELECT id, worker_name, email, phone, role FROM workers WHERE role = '$user_role'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/table.css">
    <style>
        .add-worker-btn {
            background: #FF4B2B;
            color: white;
            padding: 15px 15px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
        }
        .btn {
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
        .btn:hover {
            background: #FF4B2B;
            color: white;
        }
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 999;
        }
        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 8px;
            display: none;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            height: 450px;
        }
        .modal h3 {
            margin-bottom: 1rem;
        }
        .modal input {
            display: block;
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
        }
        .close-btn:hover {
            color: #FF4B2B;
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
            <h4><li><a href="workers.php"> <i class="fa-solid fa-users"></i> &nbsp; Workers </a></li></h4>
            <h4><li><a href="bookings.php"> <i class="fa-solid fa-book-open"></i> &nbsp; Bookings </a></li></h4>
            <h4><li><a href="history.php"> <i class="fa-solid fa-clock-rotate-left"></i> &nbsp; History </a></li></h4>
            <h4><li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>
        </ul>
    </aside>

    <main class="main-content">
        <section class="overview">
            <button id="addWorkerBtn" class="add-worker-btn"><i class="fa-solid fa-user-plus"></i></button><br><br>
            <h2 align="center">Workers List</h2><br>
            <table>
                <thead>
                    <tr>
                        <th>Worker Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>{$row['worker_name']}</td>
                                    <td>{$row['email']}</td>
                                    <td>{$row['phone']}</td>
                                    <td>{$row['role']}</td>
                                    <td>
                                        <button 
                                            class='btn' 
                                            onclick=\"window.location.href='worker_services.php?worker_id={$row['id']}'\">
                                            View Assigned Services
                                        </button>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No workers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<!-- Modal -->
<div class="modal-overlay" id="modalOverlay"></div>
<div class="modal" id="addWorkerModal">
    <button id="closeModal" class="close-btn">&times;</button>
    <h3>Add Worker</h3>
    <form id="addWorkerForm" action="add_worker.php" method="POST">
        <input type="text" id="worker_name" name="worker_name" placeholder="Worker Name" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="text" id="phone" name="phone" placeholder="Phone" required>
        <input type="text" id="role" name="role" placeholder="Role" required>
        <button type="submit" class="btn">Add Worker</button>
    </form>
</div>

<script>
    const modalOverlay = document.getElementById('modalOverlay');
    const addWorkerModal = document.getElementById('addWorkerModal');
    const addWorkerBtn = document.getElementById('addWorkerBtn');
    const closeModal = document.getElementById('closeModal');

    // Show modal
    addWorkerBtn.addEventListener('click', () => {
        modalOverlay.style.display = 'block';
        addWorkerModal.style.display = 'block';
    });

    // Close modal
    closeModal.addEventListener('click', () => {
        modalOverlay.style.display = 'none';
        addWorkerModal.style.display = 'none';
    });

    // Form validation
    document.getElementById('addWorkerForm').addEventListener('submit', function (event) {
        const phoneInput = document.getElementById('phone').value;

        // Validate phone number contains only numbers
        if (!/^\d+$/.test(phoneInput)) {
            alert('Invalid credentials: Phone number must contain only numbers.');
            event.preventDefault(); // Prevent form submission
        }
    });
</script>

</body>
</html>
