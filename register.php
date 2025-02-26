<?php 

include("connect.php");

if(isset($_POST['signUp'])){
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $phNumber = $_POST['number'];
    $password = $_POST['password'];

    // Validation Regex
    $emailRegex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $passwordRegex = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/";
    $phoneRegex = "/^\d{10}$/";
    $nameRegex = "/^[a-zA-Z\s]+$/"; // Ensuring no numbers or symbols in names

    // Validate First Name
    if (!preg_match($nameRegex, $firstName)) {
        echo "First Name should only contain letters and spaces.";
        exit();
    }

    // Validate Last Name
    if (!preg_match($nameRegex, $lastName)) {
        echo "Last Name should only contain letters and spaces.";
        exit();
    }

    // Validate Email Format
    if (!preg_match($emailRegex, $email)) {
        echo "Invalid email format.";
        exit();
    }

    // Validate Password Format
    if (!preg_match($passwordRegex, $password)) {
        echo "Password must be at least 8 characters long, contain at least one letter, one number, and one special character.";
        exit();
    }

    // Validate Phone Number Format
    if (!preg_match($phoneRegex, $phNumber)) {
        echo "Phone number must be exactly 10 digits.";
        exit();
    }

    $password = md5($password);

    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);
    if ($result->num_rows > 0) {
        echo "Email Address Already Exists!";
    } else {
        $insertQuery = "INSERT INTO users(firstName, lastName, email, phNumber, password)
                        VALUES ('$firstName', '$lastName', '$email', '$phNumber', '$password')";
        if ($conn->query($insertQuery) === TRUE) {
            header("location: index.php");
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password = md5($password);
    
    $sql = "SELECT * FROM users WHERE email='$email' and password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        session_start();
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        $_SESSION['role'] = $row['role'];
        
        if ($row['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: homepage.php");
        }
        exit();
    } else {
        echo "Incorrect Email or Password";
    }
}
?>
<br><a href="index.php">Retry</a>
