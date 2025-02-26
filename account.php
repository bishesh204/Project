<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['email'];
$user_query = $conn->query("SELECT * FROM users WHERE email = '$user_email'");
$user = $user_query->fetch_assoc();

if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    $sql = "DELETE FROM bookings WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
}

$bookings_query = $conn->query("SELECT bookings.*, cars.name as car_name, cars.image as car_image 
                                FROM bookings 
                                JOIN cars ON bookings.car_id = cars.id 
                                WHERE bookings.user_id = {$user['id']}");

$bookings = $bookings_query->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account - Ontrack Rentals</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        body {
            background-color: #121212;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2%;
            color: #ffffff;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
        }

        .navbar h1 {
            color: #1DB954;
            font-size: 1.5rem;
        }

        .navbar a {
            color: #ffffff;
            margin-left: 1rem;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #1DB954;
        }

        .account-section {
            margin-top: 6rem;
            width: 90%;
            max-width: 1200px;
            padding: 2rem;
            background: #1E1E1E;
            border-radius: 10px;
            flex-grow: 1;
        }

        .account-section h2 {
            font-size: 2.5rem;
            color: #1DB954;
            margin-bottom: 1rem;
        }

        .account-section table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            background: #1DB954;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }

        .btn:hover {
            background: #138A3E;
        }

        .footer {
            padding: 20px 0;
            text-align: center;
            width: 100%;
            background-color: #000000;
            color: #ffffff;
            position: relative;
            bottom: 0;
        }

        .footer-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .footer-left,
        .footer-right {
            width: 45%;
        }

        .footer-left h2,
        .footer-right h3 {
            margin-top: 0;
            color: #1DB954;
        }

        .footer-bottom {
            padding: 10px 0;
        }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="logo"><img src="logo.png" alt="logo" width="100px"></div>
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="account.php">Account</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </nav>

    <div class="account-section">
        <h2>Your Bookings</h2>
        <?php if (count($bookings) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Car</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($booking['car_image']); ?>" alt="<?php echo htmlspecialchars($booking['car_name']); ?>" width="100"></td>
                            <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['status']); ?></td>
                            <td>
                                <?php if ($booking['status'] !== 'Cancelled'): ?>
                                    <form method="post">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="cancel_booking" class="btn">Cancel Booking</button>
                                    </form>
                                <?php else: ?>
                                    <span>Cancelled</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no bookings.</p>
        <?php endif; ?>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <h2>About Us</h2>
                <p>CarRent is committed to providing the best car rental service with a wide range of premium vehicles at competitive prices. Our mission is to make your journeys comfortable, safe, and memorable.</p>
            </div>
            <div class="footer-right">
                <h3>Contact Information</h3>
                <p>Email: info@ontrackrentals.com</p>
                <p>Phone: +977 9742503545</p>
                <p>Address: Hetauda, Nepal</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2024 CarRent. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
