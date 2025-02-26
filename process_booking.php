<?php
include('connect.php');
session_start();

// Ensure the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Check if the action and booking_id are set
if (isset($_POST['action']) && isset($_POST['booking_id'])) {
    $action = $_POST['action'];
    $booking_id = $_POST['booking_id'];

    // Validate the action
    if ($action !== 'approve' && $action !== 'reject') {
        echo "Invalid action.";
        exit();
    }

    // Update the booking status
    $status = ($action === 'approve') ? 'approved' : 'rejected';
    $sql = "UPDATE bookings SET status = '$status' WHERE booking_id = $booking_id";

    if ($conn->query($sql) === TRUE) {
        echo "Booking status updated successfully.";
        header("Location: admin.php");  // Redirect back to admin page
    } else {
        echo "Error updating booking status: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}
?>
