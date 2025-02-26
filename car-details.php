<?php
session_start();
include("connect.php");

if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$car_id = isset($_GET['id']) ? $_GET['id'] : 0;
$sql = "SELECT * FROM cars WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $car_id);
$stmt->execute();
$result = $stmt->get_result();
$car = $result->fetch_assoc();

if (!$car) {
    header("Location: homepage.php");
    exit();
}

if (isset($_POST['book_car'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $user_email = $_SESSION['email'];

    $user_sql = "SELECT id FROM users WHERE email = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("s", $user_email);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();

    // Check for existing bookings within the requested date range (Corrected)
    $check_booking_sql = "SELECT 1 FROM bookings 
                        WHERE car_id = ? 
                        AND status = 'approved'  -- Check only approved bookings
                        AND NOT (end_date < ? OR start_date > ?)"; // Corrected logic

    $check_booking_stmt = $conn->prepare($check_booking_sql);
    $check_booking_stmt->bind_param("iis", $car_id, $start_date, $end_date); // Corrected bind_param
    $check_booking_stmt->execute();
    $check_booking_stmt->store_result();
    $existing_booking = $check_booking_stmt->num_rows > 0;

    if ($existing_booking) {
        $error_message = "This car is already booked for the selected dates. Please choose different dates.";
    } else {
        $booking_sql = "INSERT INTO bookings (user_id, car_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'pending')";
        $booking_stmt = $conn->prepare($booking_sql);
        $booking_stmt->bind_param("iiss", $user['id'], $car_id, $start_date, $end_date);

        if ($booking_stmt->execute()) {
            $success_message = "Booking request submitted successfully!";
        } else {
            $error_message = "Error submitting booking. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $car['name']; ?> - Ontrack Rentals</title>
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
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2%;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
        }

        .navbar a {
            color: #ffffff;
            margin-left: 1rem;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .car-details {
            background: #1E1E1E;
            padding: 2rem;
            border-radius: 10px;
        }

        .car-image {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .car-info h1 {
            color: #1DB954;
            margin-bottom: 1rem;
        }

        .price {
            font-size: 1.5rem;
            color: #1DB954;
            margin: 1rem 0;
        }

        .booking-form {
            background: #1E1E1E;
            padding: 2rem;
            border-radius: 10px;
            position: sticky;
            top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #1DB954;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            background: #333;
            border: 1px solid #555;
            border-radius: 5px;
            color: white;
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: #1DB954;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #138A3E;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1.5rem 0;
        }

        .feature {
            background: #333;
            padding: 1rem;
            border-radius: 5px;
            text-align: center;
        }

        .feature i {
            color: #1DB954;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .success-message {
            background: #1DB954;
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .error-message {
            background: #dc3545;
            color: white;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="logo.png" alt="logo" width="100px">
        </div>
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="homepage.php#cars">Cars</a>
            <a href="homepage.php#about">About</a>
            <a href="account.php">Account</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div class="car-details">
            <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>" class="car-image">
            <div class="car-info">
                <h1><?php echo htmlspecialchars($car['name']); ?></h1>
                <p><?php echo htmlspecialchars($car['description']); ?></p>
                <div class="price">Rs.<?php echo htmlspecialchars($car['price']); ?> per day</div>
                
                <div class="features">
                    <div class="feature">
                        <i class="fas fa-car"></i>
                        <p>4 Seats</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-gas-pump"></i>
                        <p>Fuel Efficient</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-cog"></i>
                        <p>Automatic</p>
                    </div>
                    <div class="feature">
                        <i class="fas fa-suitcase"></i>
                        <p>Large Trunk</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="booking-form">
            <h2>Book This Car</h2>
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <button type="submit" name="book_car" class="btn">Book Now</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
        });
    </script>
</body>
</html>