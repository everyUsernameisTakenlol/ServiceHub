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

// Fetch services from the database
$sql = "SELECT id, service_name, description, image, price, duration, included_tasks, availability, service_image FROM services";
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
        .modal-content p {
            text-align: left; /* Align text to the left */
            margin-bottom: 10px; /* Add spacing between paragraphs */
        }

        .modal-content ul {
            text-align: left; /* Align text to the left */
            list-style-position: inside; /* Align bullets with text */
            padding-left: 0; /* Remove default padding from ul */
            margin-left: 0; /* Ensure alignment with paragraphs */
        }

        .modal-content ul li {
            margin-bottom: 5px; /* Add spacing between list items */
        }

        #serviceImage {
            max-width: 100%;
            height: auto;
            margin: 0 auto;
            display: block;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<?php
// Display error or success messages from session
if (isset($_SESSION['error_message'])) {
    echo "<script>alert('" . $_SESSION['error_message'] . "');</script>";
    unset($_SESSION['error_message']); // Clear the message after displaying
} elseif (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . $_SESSION['success_message'] . "');</script>";
    unset($_SESSION['success_message']); // Clear the message after displaying
}
?>

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
            <h2 class="section-title">Available Services</h2>
            <div class="top-services">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="service-card">
                            <img src="../img/<?php echo $row['image']; ?>" alt="<?php echo $row['service_name']; ?>">
                            <h3><?php echo $row['service_name']; ?></h3>
                            <button class="learn-more" 
                                    data-id="<?php echo $row['id']; ?>" 
                                    data-name="<?php echo $row['service_name']; ?>" 
                                    data-description="<?php echo $row['description']; ?>"
                                    data-price="<?php echo $row['price']; ?>" 
                                    data-duration="<?php echo $row['duration']; ?>" 
                                    data-included="<?php echo htmlspecialchars($row['included_tasks']); ?>" 
                                    data-availability="<?php echo $row['availability']; ?>"
                                    data-image="<?php echo $row['service_image']; ?>"> <!-- Add the data-image attribute -->
                                Learn More
                            </button>


                            <br><br>
                            <button class="book-now" data-service="<?php echo $row['service_name']; ?>">Book Now</button>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No services available at the moment.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Book Service</h2>
        <form id="bookingForm" action="book_service.php" method="POST">
            <input type="hidden" id="serviceName" name="service_name">
            
            <label for="address">House No., Barangay, City:</label>
            <input type="text" id="address" name="address" required>

            <label for="province">Province, Postal Code:</label>
            <input type="text" id="province" name="province" required>

            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>

            <label for="time">Time:</label>
            <input type="time" id="time" name="time" required>



            <label for="payment">Payment Method:</label>
            <select id="payment" name="payment_method" required>
                <option value="Cash">Cash</option>
                <!-- <option value="Card">Card</option>
                <option value="Online">Online</option> -->
            </select>

            <button type="submit" class="submit-btn">Confirm Booking</button>

            
        </form>
    </div>
</div>


<!-- Learn More Modal -->
<div id="learnMoreModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2 align="center" id="serviceTitle"></h2><br>
        <img id="serviceImage" src="" alt="Service Image" style="max-width: 90%; height: 90%; margin-bottom: 20px;">
        <p><strong>Description:</strong> <span id="serviceDescription"></span></p>
        <p><strong>Price:</strong> <span id="servicePrice"></span></p> <!-- Price label added -->
        <p><strong>Duration:</strong> <span id="serviceDuration"></span></p> <!-- Duration label added -->
        <p><strong>Availability:</strong> <span id="serviceAvailability"></span></p> <!-- Availability label added -->
        <ul><strong>Included Tasks:</strong>
            <span id="serviceIncluded"></span> <!-- Included tasks listed here -->
        </ul>



    </div>
</div>



<script>
    document.addEventListener("DOMContentLoaded", () => {
    const bookingModal = document.getElementById("bookingModal");
    const closeBtn = document.querySelector(".close-btn");
    const bookNowBtns = document.querySelectorAll(".book-now");
    const timeInput = document.getElementById("time");
    const serviceNameInput = document.getElementById("serviceName");
    const bookingForm = document.getElementById("bookingForm");
    const dateInput = document.getElementById("date");

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

    // Open Booking Modal
    bookNowBtns.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const serviceName = e.target.getAttribute("data-service");
            serviceNameInput.value = serviceName;
            bookingModal.style.display = "flex";
        });
    });

    // Close Booking Modal
    closeBtn.addEventListener("click", () => {
        bookingModal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === bookingModal) {
            bookingModal.style.display = "none";
        }
    });

    // Disable times between 5:00 PM and 8:00 AM
    timeInput.addEventListener("input", () => {
        const selectedTime = timeInput.value;
        if (isTimeDisabled(selectedTime)) {
            alert("This time is not available for booking. Please select a time between 8:00 AM and 5:00 PM.");
            timeInput.value = ""; // Clear the invalid time
        }
    });

    // Validate the date and time before confirming the booking
    bookingForm.addEventListener("submit", (e) => {
        const selectedDate = dateInput.value;
        const selectedTime = timeInput.value;

        if (isDateInThePast(selectedDate)) {
            e.preventDefault(); // Prevent the form from submitting
            alert("This date is in the past. Please select a future date.");
        } else if (!isDateTimeValid(selectedDate, selectedTime)) {
            e.preventDefault(); // Prevent the form from submitting
            alert("This date and time are in the past. Please select a future date and time.");
        }

        
    });
});






    //Learn more modal
    document.addEventListener("DOMContentLoaded", () => {
    const learnMoreModal = document.getElementById("learnMoreModal");
    const closeBtns = document.querySelectorAll(".close-btn");
    const learnMoreBtns = document.querySelectorAll(".learn-more");
    const serviceTitle = document.getElementById("serviceTitle");
    const serviceDescription = document.getElementById("serviceDescription");
    const servicePrice = document.getElementById("servicePrice");
    const serviceDuration = document.getElementById("serviceDuration");
    const serviceIncluded = document.getElementById("serviceIncluded");
    const serviceAvailability = document.getElementById("serviceAvailability");
    const serviceImage = document.getElementById("serviceImage");  // Add this line to get the image element

    // Open Learn More Modal
    learnMoreBtns.forEach((btn) => {
        btn.addEventListener("click", (e) => {
            const serviceName = e.target.getAttribute("data-name");
            const serviceDesc = e.target.getAttribute("data-description");
            const price = e.target.getAttribute("data-price");
            const duration = e.target.getAttribute("data-duration");
            const included = e.target.getAttribute("data-included");
            const availability = e.target.getAttribute("data-availability");
            const image = e.target.getAttribute("data-image"); // Fetch the image data

            // Populate modal fields
            serviceTitle.textContent = serviceName;
            serviceImage.src = `../img/${image}`; // Set the image source dynamically from the 'service_image' column
            serviceDescription.textContent = serviceDesc;
            servicePrice.textContent = `₱${price}`;
            serviceDuration.textContent = `${duration}`;
            serviceAvailability.textContent = `${availability}`;

            // Set the image source
            serviceImage.src = `../img/${image}`; // Set the image source dynamically

            // Parse included tasks if it's a JSON string or comma-separated list
            const includedTasks = included.includes("[") ? JSON.parse(included) : included.split(",");
            serviceIncluded.innerHTML = includedTasks.map(task => `<li>${task.trim()}</li>`).join("");

            serviceAvailability.textContent = availability;

            // Show modal
            learnMoreModal.style.display = "flex";
        });
    });

    // Close modal when clicking close button
    closeBtns.forEach((btn) => {
        btn.addEventListener("click", () => {
            learnMoreModal.style.display = "none";
        });
    });

        // Close modal when clicking outside of the modal content (Learn More)
        window.addEventListener("click", (e) => {
        if (e.target === learnMoreModal) {
            learnMoreModal.style.display = "none";
        }
    });
});
</script>

</body>
</html>
<?php
$conn->close();
?>
