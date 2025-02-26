<?php
session_start();
include("connect.php");

if (!isset($_SESSION['user_id']) || !isset($_POST['booking_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$booking_id = $_POST['booking_id'];

$query = "SELECT * FROM bookings WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if ($booking) {
    $update_query = "UPDATE bookings SET status = 'Canceled' WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("i", $booking_id);
    $update_stmt->execute();

    header("Location: account.php");
    exit();
} else {
    echo "Booking not found or you're not authorized to cancel it.";
}
?>
