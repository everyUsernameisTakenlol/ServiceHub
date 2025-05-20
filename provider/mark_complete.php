<?php
require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    $query = "UPDATE bookings SET status = 'Completed' WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);

    if ($stmt->execute()) {
        echo "Booking marked as completed successfully.";
    } else {
        echo "Failed to update booking status.";
    }
}
?>
