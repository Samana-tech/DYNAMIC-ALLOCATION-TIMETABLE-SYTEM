<?php
// Start session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "timetabledb";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staff_no = $_POST['staff_no'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone_no = $_POST['phone_no'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo '<script>alert("Passwords do not match!");</script>';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (staff_no, name, email, phone_no, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $staff_no, $name, $email, $phone_no, $hashed_password);

        // Execute the statement
        if ($stmt->execute()) {
            echo '<script>
                    alert("Registration successful!");
                    window.location.href = "index.php";
                  </script>';
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- BOOTSTRAP CORE STYLE CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!-- FONT AWESOME CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
    <!-- FLEXSLIDER CSS -->
    <link href="assets/css/flexslider.css" rel="stylesheet"/>
    <!-- CUSTOM STYLE CSS -->
    <link href="assets/css/style.css" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'/>

    <style>
        .container {
            margin-top: 0px;
            max-width: 500px;
            /*background-color: #fff;*/
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-color: #ffffff;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-default {
            background-color: #007bff;
            color: #fff;
            border-color: #007bff;
        }
        .btn-default:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        body {
    position: relative;
    background-image: url('assets/img/bg/img5.jpeg');
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
    font-family: Arial, sans-serif;
    color: #333;
    margin: 0; /* Remove default margin to cover the entire viewport */
    padding: 0; /* Remove default padding */
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5); /* Dark overlay color */
    z-index: -1;
}
    table {
    margin-top: 10px;
    margin-bottom: 3px;
    font-family: Arial, sans-serif;
    border-collapse: collapse;
    width: 80%;
    background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* More pronounced shadow */
    border-radius: 10px; /* Rounded corners */
    overflow: hidden; /* Ensures the shadow doesn't overflow */
}

    td, th {
        border: 1px solid #dddddd;
        text-align: center;
        padding: 10px;
    }


    tr:nth-child(even) td {
        background-color: yellow;
    }

    tr:nth-child(odd) td {
        background-color: white;
    }

    .btn-info {
        background-color: #5bc0de; /* Blue color for info button */
        border-color: #46b8da;
        color: #ffffff; /* White text color */
        padding: 10px 20px;
        font-size: 16px;
        transition: background-color 0.3s ease;
        
    }

    .btn-info:hover {
        background-color: #31b0d5; /* Darker shade on hover */
    }

    .center-text {
        text-align: center; /* Center-align text */
        margin-bottom: 10px; /* Spacing below the centered text */
    }

    .custom-caption {
        /* background-color: #ffffff; White background */
        color: #ffffff; /* Text color */
        font-weight: bold; /* Bold text */
        padding: 10px; /* Padding around the caption */
        text-align: center; /* Center-align text */
    }
    </style>
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirm_password").value;
            if (password !== confirmPassword) {
                alert("Passwords do not match!");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
<div class="container">
    <h2 class="custom-caption">Register</h2>
    <form action="register.php" method="POST" onsubmit="return validateForm()">
        <div class="form-group">
            <label for="staff_no" class="custom-caption">Staff No.</label>
            <input type="text" class="form-control" id="staff_no" name="staff_no" required>
        </div>
        <div class="form-group">
            <label for="name" class="custom-caption">Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email" class="custom-caption">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="phone_no" class="custom-caption">Phone No.</label>
            <input type="text" class="form-control" id="phone_no" name="phone_no" required>
        </div>
        <div class="form-group">
            <label for="password" class="custom-caption">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password" class="custom-caption">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-default btn-block">Register</button>
    </form>
</div>

<!--  Jquery Core Script -->
<script src="assets/js/jquery-1.10.2.js"></script>
<!--  Core Bootstrap Script -->
<script src="assets/js/bootstrap.js"></script>
<!--  Flexslider Scripts -->
<script src="assets/js/jquery.flexslider.js"></script>
<!--  Scrolling Reveal Script -->
<script src="assets/js/scrollReveal.js"></script>
<!--  Scroll Scripts -->
<script src="assets/js/jquery.easing.min.js"></script>
<!--  Custom Scripts -->
<script src="assets/js/custom.js"></script>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
</body>
</html>
