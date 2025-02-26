<?php
session_start();
include("connect.php");

    if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
        header("Location: index.php");
        exit();
    }

    // Fetch bookings (including pending ones)
    $bookings_query = "SELECT bookings.*, cars.name AS car_name, users.email AS user_email, cars.image AS car_image, users.firstName, users.lastName
    FROM bookings 
    JOIN cars ON bookings.car_id = cars.id 
    JOIN users ON bookings.user_id = users.id
    ORDER BY bookings.id DESC";

$bookings_result = $conn->query($bookings_query);
$bookings = $bookings_result->fetch_all(MYSQLI_ASSOC);


    if(isset($_POST['add_car'])) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_name = $_FILES['image']['name'];
            $image_tmp_name = $_FILES['image']['tmp_name'];
            $image_size = $_FILES['image']['size'];
            $image_error = $_FILES['image']['error'];

            $allowed_types = ['image/jpeg', 'image/png'];
            $max_size = 5 * 1024 * 1024;

            if(in_array($_FILES['image']['type'], $allowed_types) && $image_size <= $max_size) {
                $upload_dir = 'uploads/cars/';
                $image_path = $upload_dir . basename($image_name);

                if(move_uploaded_file($image_tmp_name, $image_path)) {
                    $sql = "INSERT INTO cars (name, description, price, image) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssds", $name, $description, $price, $image_path);
                    $stmt->execute();
                } else {
                    echo "Failed to upload image.";
                }
            } else {
                echo "Invalid image file type or size exceeded.";
            }
        } else {
            echo "No image uploaded or error occurred.";
        }
    }

    if(isset($_POST['delete_car'])) {
        $car_id = $_POST['car_id'];
        $sql = "DELETE FROM cars WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
    }

// Handle Accept/Reject actions (NEW)
    if (isset($_POST['action']) && isset($_POST['booking_id'])) {
        $action = $_POST['action'];
        $booking_id = $_POST['booking_id'];

        if ($action !== 'approve' && $action !== 'reject') {
            echo "Invalid action.";
            exit();
        }

        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $update_query = "UPDATE bookings SET status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $status, $booking_id);

        if ($update_stmt->execute()) {
            header("Location: admin.php"); // Refresh the page after update
            exit();
        } else {
            echo "Error updating booking status: " . $update_stmt->error;
        }
    }


    $cars = $conn->query("SELECT * FROM cars");

    $users = $conn->query("SELECT * FROM users");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Ontrack Rentals</title>
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
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #1E1E1E;
            padding: 2rem;
        }

        .main-content {
            flex: 1;
            padding: 2rem;
        }

        .section {
            background: #1E1E1E;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .btn {
            padding: 0.8rem 1.5rem;
            background: #1DB954;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background: #138A3E;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #333;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        input, textarea {
            width: 100%;
            padding: 0.5rem;
            background: #333;
            border: 1px solid #555;
            color: white;
            border-radius: 5px;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #1DB954;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
        }

        .action-buttons button {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .approve-button {
            background-color: #4CAF50; /* Green */
            color: white;
        }

        .reject-button {
            background-color: #f44336; /* Red */
            color: white;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <h2 style="color: #1DB954; margin-bottom: 2rem;">Admin Dashboard</h2>
            <nav>
                <ul style="list-style: none;">
                    <li><a href="#dashboard" class="btn" style="display: block; margin-bottom: 1rem;">Dashboard</a></li>
                    <li><a href="#cars" class="btn" style="display: block; margin-bottom: 1rem;">Manage Cars</a></li>
                    <li><a href="#users" class="btn" style="display: block; margin-bottom: 1rem;">Users</a></li>
                    <li><a href="#bookings" class="btn" style="display: block; margin-bottom: 1rem;">Bookings</a></li>
                    <li><a href="logout.php" class="btn" style="display: block;">Logout</a></li>
                </ul>
            </nav>
        </div>

        <div class="main-content">
            <div class="stats-container">
                <div class="stat-card">
                    <h3><?php echo $cars->num_rows; ?></h3> <p>Total Cars</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $users->num_rows; ?></h3> <p>Total Users</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count($bookings); ?></h3> <p>Total Bookings</p> <! -- Corrected line -->
                </div>
            </div>

            <div id="cars" class="section">
                <h2>Manage Cars</h2>
                <form method="post" enctype="multipart/form-data" class="form-group">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="Car Name" required>
                    </div>
                    <div class="form-group">
                        <textarea name="description" placeholder="Description" required></textarea>
                    </div>
                    <div class="form-group">
                        <input type="number" name="price" placeholder="Price per Day" required>
                    </div>
                    <div class="form-group">
                        <input type="file" name="image" accept="image/*" required>
                    </div>
                    <button type="submit" name="add_car" class="btn">Add Car</button>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($car = $cars->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $car['name']; ?></td>
                            <td><?php echo $car['description']; ?></td>
                            <td>Rs.<?php echo $car['price']; ?>/day</td>
                            <td>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                    <button type="submit" name="delete_car" class="btn" style="background: #dc3545;">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div id="users" class="section">
                <h2>Users</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['firstName'] . ' ' . $user['lastName']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['phNumber']; ?></td>
                            <td><?php echo $user['role'] ?? 'user'; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div id="bookings" class="section">
                    <table>
                        <thead>
                            <tr>
                                <th>Booking ID</th> <th>User</th>
                                <th>Car Image</th> <th>Car</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo $booking['id']; ?></td>
                                    <td><?php echo htmlspecialchars($booking['firstName'] . ' ' . $booking['lastName']); ?></td>
                                    <td><img src="<?php echo $booking['car_image']; ?>" alt="<?php echo $booking['car_name']; ?>" width="50"></td>
                                    <td><?php echo htmlspecialchars($booking['car_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['start_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['end_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['status']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="approve-button" <?php if ($booking['status'] !== 'pending') echo 'disabled'; ?>>Approve</button>
                                            </form>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="reject-button" <?php if ($booking['status'] !== 'pending') echo 'disabled'; ?>>Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>
    </div>
</body>
</html>
