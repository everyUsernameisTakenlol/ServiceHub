<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "home";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch notifications for completed bookings including worker roles
$notification_query = "
    SELECT b.id, b.service_name, w.worker_name, w.role 
    FROM bookings AS b
    INNER JOIN workers AS w ON b.worker_id = w.id
    WHERE b.username = '{$_SESSION['username']}' 
    AND b.status = 'Completed' 
    AND b.user_notified = 0";
$result_notifications = $conn->query($notification_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiceHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/dashboard.css">
    <!-- Leaflet CSS -->
    <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


    <style>
        .modal {
  display: none;
  position: fixed;
  z-index: 10;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background: rgba(0,0,0,0.5);
}

.modal-content {
  background: #fff;
  margin: 10% auto;
  padding: 20px;
  width: 80%;
  max-width: 500px;
  position: relative;
  border-radius: 10px;
}

.close {
  position: absolute;
  right: 15px;
  top: 10px;
  font-size: 24px;
  cursor: pointer;
}

    </style>
</head>
<body>
<?php if ($result_notifications->num_rows > 0): ?>
    <?php 
    while ($notification = $result_notifications->fetch_assoc()) {
        // Extract worker and role details from the current row
        $worker_name = $notification['worker_name'] ?: 'N/A';
        $worker_role = $notification['role'] ?: 'N/A'; // Replace with 'N/A' if role is missing
        ?>
        <script>
            alert("You have a new completed booking. Check the details in history!\nYour Worker is: **<?php echo $worker_name; ?>**, Role: **<?php echo $worker_role; ?>**");
        </script>
    <?php 
    }
    ?>
    <?php 
    // Mark notifications as seen
    $update_notify_query = "
        UPDATE bookings 
        SET user_notified = 1 
        WHERE username = '{$_SESSION['username']}' 
        AND status = 'Completed'";
    $conn->query($update_notify_query);
    ?>
<?php endif; ?>
<header>
    <img src="../img/asd.png" alt="Logo" width="55" height="55">
    <a href="dashboard.php"><h2>ServiceHub  </h2></a>
    <div class="search-bar">
        <!-- <input type="text" placeholder="Search services..."> -->
    </div>
    <div class="profile">
        <h4><i class="fa-regular fa-user"></i> &nbsp; <?php echo $_SESSION['username']; ?></h4>
    </div>
</header>

<div class="dashboard-container">
    <aside class="sidebar">
        <ul>
            <h4><li><a href="browse.php"> <i class="fa-solid fa-magnifying-glass"></i> &nbsp; Services</a></li></h4>
            <h4><li><a href="requests.php"> <i class="fa-solid fa-receipt"></i> &nbsp; Requests</a></li></h4>
            <h4><li><a href="history.php"> <i class="fa-solid fa-clock-rotate-left"></i> &nbsp; History </a></li></h4>
            <h4><li><a href="address.php"> <i class="fa-solid fa-house"></i> &nbsp; Address</a></li></h4>
            <h4><li><a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i> &nbsp; Log out</a></li></h4>

        </ul>
    </aside>

    <main class="main-content">
        <section class="overview">
            <button onclick="openModal()">Add Address</button>

        </section>
    </main>
</div>

<div id="addressModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Add Your Address</h3>
    <form id="addressForm">
      <input type="text" id="postalCode" placeholder="Postal Code" required><br>
      <input type="text" id="street" placeholder="Street Name" required><br>
      <input type="text" id="building" placeholder="Building / House No." required><br>
      
      <div id="map" style="height: 300px; width: 100%; margin-top: 10px;"></div>
      
      <input type="hidden" id="latitude" name="latitude">
      <input type="hidden" id="longitude" name="longitude">
      <br>
      <button type="submit">Save Address</button>
    </form>
  </div>
</div>


<script src="script.js"></script>
<script>
let map, marker;

function openModal() {
  document.getElementById('addressModal').style.display = 'block';

  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition((position) => {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;

      initMap(lat, lng);
    }, () => {
      // fallback to default location
      initMap(14.5995, 120.9842); // Manila, Philippines
    });
  } else {
    alert("Geolocation not supported.");
    initMap(14.5995, 120.9842);
  }
}

function initMap(lat, lng) {
  if (map) {
    map.remove(); // Reset if already initialized
  }

  map = L.map('map').setView([lat, lng], 16);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  marker = L.marker([lat, lng], { draggable: true }).addTo(map);

  document.getElementById('latitude').value = lat;
  document.getElementById('longitude').value = lng;

  marker.on('dragend', function(e) {
    const pos = marker.getLatLng();
    document.getElementById('latitude').value = pos.lat;
    document.getElementById('longitude').value = pos.lng;
  });
}

document.querySelector('.close').onclick = () => {
  document.getElementById('addressModal').style.display = 'none';
};

window.onclick = function (e) {
  if (e.target == document.getElementById('addressModal')) {
    document.getElementById('addressModal').style.display = 'none';
  }
};

document.getElementById('addressForm').onsubmit = function (e) {
  e.preventDefault();
  const postal = document.getElementById('postalCode').value;
  const street = document.getElementById('street').value;
  const building = document.getElementById('building').value;
  const lat = document.getElementById('latitude').value;
  const lng = document.getElementById('longitude').value;

  console.log({ postal, street, building, lat, lng });

  alert("Address saved successfully!");
  document.getElementById('addressModal').style.display = 'none';

  // TODO: send data via AJAX or form submission to PHP
};
</script>

</body>
</html>
