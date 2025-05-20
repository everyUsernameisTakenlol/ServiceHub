<?php
session_start(); 

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit;
}

$host = 'localhost'; 
$username = 'root'; 
$password = ''; 
$dbname = 'home'; 

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, name, email, phone, location FROM users";
$result = $conn->query($sql);

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
    <style>
        table {
            width: 100%;
            max-width: none;
            border-collapse: collapse;
            font-size: 18px;
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
            <h2 align="center">Users List</h2><br>
            <!-- Table displaying users -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data for each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row['id'] . "</td>
                                    <td>" . $row['name'] . "</td>
                                    <td>" . $row['email'] . "</td>
                                    <td>" . $row['phone'] . "</td>
                                    <td>" . $row['location'] . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<script src="script.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
