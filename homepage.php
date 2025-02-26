<?php
session_start();
include("connect.php");
$cars_query = $conn->query("SELECT * FROM cars ORDER BY id DESC");
$all_cars = $cars_query->fetch_all(MYSQLI_ASSOC);
$featured_car = !empty($all_cars) ? $all_cars[0] : null;
$remaining_cars = array_slice($all_cars, 1);

$search_results = [];
if (isset($_POST['search'])) {
    $search_term = $conn->real_escape_string($_POST['search_term']);
    $search_query = $conn->query("SELECT * FROM cars WHERE name LIKE '%$search_term%' OR description LIKE '%$search_term%' ORDER BY id DESC");
    $search_results = $search_query->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ontrack Rentals - Homepage</title>
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
            align-items: center;
            padding-top: 70px;
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

        .navbar .nav-links {
            display: flex;
            align-items: center;
        }

        .navbar .nav-links a {
            color: #ffffff;
            margin-left: 1rem;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .navbar .nav-links a:hover {
            color: #1DB954;
        }

        .navbar .search-form {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .navbar .search-form input[type="text"] {
            padding: 0.5rem 0.8rem;
            font-size: 0.9rem;
            border: 2px solid #1DB954;
            border-radius: 5px;
            background-color: #1E1E1E;
            color: #ffffff;
            transition: border-color 0.3s, box-shadow 0.3s;
            width: 200px;
        }

        .navbar .search-form input[type="text"]:focus {
            outline: none;
            border-color: #138A3E;
            box-shadow: 0 0 8px #1DB954;
        }

        .navbar .search-form button {
            padding: 0.5rem;
            font-size: 0.9rem;
            color: #ffffff;
            background-color: #1DB954;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .navbar .search-form button:hover {
            background-color: #138A3E;
            transform: scale(1.05);
        }

        .navbar .search-form button i {
            font-size: 1rem;
        }
        .home {
            background: url('homeCar.jpg') no-repeat center center / cover;
            height: 100vh;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .home, .car-list {
            margin-top: 70px;
        }

        .home h2 {
            font-size: 4.8rem;
            color: #1DB954;
            text-align: start;
        }

        .featured-car {
            background: #1E1E1E;
            border-radius: 10px;
            max-width: 800px;
            width: 90%;
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            color: #fff;
            text-align: center;
            margin-top: 2rem;
        }

        .featured-car img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .featured-car h2 {
            margin: 1rem 0;
            font-size: 1.8rem;
            color: #1DB954;
        }

        .featured-car p {
            font-size: 1.1rem;
            color: #aaaaaa;
            margin-bottom: 1rem;
        }

        .featured-car .btn {
            padding: 0.8rem 2rem;
            color: #121212;
            background: #1DB954;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }

        .featured-car .btn:hover {
            background: #138A3E;
        }

        .featured-car:hover {
            transform: scale(1.05);
            transition: transform (0.3);
        }

        .car-list {
            width: 90%;
            max-width: 1000px;
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 2rem;
            justify-content: space-between;
        }

        .car-card {
            background: #1E1E1E;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: calc(33.333% - 1rem);
            min-height: 350px;
        }

        @media (max-width: 768px) {
            .car-card {
                width: calc(50% - 1rem);
            }
        }

        @media (max-width: 480px) {
            .car-card {
                width: 100%;
            }
        }
        .car-card:hover {
            transform: scale(1.05);
            transition: transform 0.3s;
        }
        .car-card img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .car-card h3 {
            margin: 1rem 0;
            color: #1DB954;
        }

        .car-card p {
            color: #aaaaaa;
            margin-bottom: auto;
        }

        .car-card .btn {
            padding: 0.6rem 1.2rem;
            color: #121212;
            background: #1DB954;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: auto;
        }

        .car-card .btn:hover {
            background: #138A3E;
        }

        footer {
            padding: 20px 0;
            text-align: center;
            width: 100%;
            background-color: #000000;
            color: #ffffff;
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
        <div class="logo">
            <img src="logo.png" alt="logo" width="100px">
        </div>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="#cars">Cars</a>
            <a href="#about">About</a>
            <a href="account.php">Account</a>
            <a href="logout.php" class="btn">Logout</a>
        </div>
        <form method="POST" action="" class="search-form">
            <input type="text" name="search_term" placeholder="Search cars by name..." required>
            <button type="submit" name="search"><i class="fas fa-search"></i></button>
        </form>
    </nav>
    <?php if (!empty($search_results)): ?>
        <div class="car-list">
            <?php foreach ($search_results as $car): ?>
                <div class="car-card">
                    <img src="<?php echo $car['image']; ?>" alt="<?php echo $car['name']; ?>">
                    <h3><?php echo $car['name']; ?></h3>
                    <p><?php echo $car['description']; ?></p>
                    <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="home" id="home"> 
            <h2>Find Your Perfect Ride</h2> 
    </div>

    <?php if ($featured_car): ?>
        <div class="featured-car" id="cars">
            <img src="<?php echo htmlspecialchars($featured_car['image']); ?>" alt="<?php echo htmlspecialchars($featured_car['name']); ?>">
            <h2>Featured Car: <?php echo htmlspecialchars($featured_car['name']); ?></h2>
            <p><?php echo htmlspecialchars($featured_car['description']); ?></p>
            <a href="car-details.php?id=<?php echo $featured_car['id']; ?>" class="btn">Rent Now</a>
        </div>
    <?php endif; ?>


    <div class="car-list">
        <?php foreach ($remaining_cars as $car): ?>
            <div class="car-card">
                <img src="<?php echo htmlspecialchars($car['image']); ?>" alt="<?php echo htmlspecialchars($car['name']); ?>">
                <h3><?php echo htmlspecialchars($car['name']); ?></h3>
                <p><?php echo htmlspecialchars($car['description']); ?></p>
                <div class="price">Rs.<?php echo htmlspecialchars($car['price']); ?> per day</div>
                <a href="car-details.php?id=<?php echo $car['id']; ?>" class="btn">View Details</a>
            </div>
        <?php endforeach; ?>
    </div>
    <footer id="about"> 
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
