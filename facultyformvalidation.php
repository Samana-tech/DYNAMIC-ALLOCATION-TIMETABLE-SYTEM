<?php
session_start();
include 'connection.php';

if (isset($_POST['FN']) && isset($_POST['PASS'])) {
    $staff_no = $_POST['FN'];
    $password = $_POST['PASS'];
} else {
    die("Form data not submitted correctly.");
}

$conn = mysqli_connect("localhost", "root", "", "timetabledb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details from the database
$q = mysqli_query($conn, "SELECT name, password FROM users WHERE staff_no = '$staff_no'");

if (mysqli_num_rows($q) == 1) {
    $row = mysqli_fetch_assoc($q);
    // Verify the password
    if (password_verify($password, $row['password'])) {
        $_SESSION['loggedin_name'] = $row['name'];
        $_SESSION['loggedin_id'] = $staff_no;
        header("Location: lecturerpage.php");
        exit();
    } else {
        $message = "Invalid Password.\\nTry again.";
        echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
    }
} else {
    $message = "Invalid Faculty Number.\\nTry again.";
    echo "<script type='text/javascript'>alert('$message'); window.history.back();</script>";
}

$conn->close();
?>
