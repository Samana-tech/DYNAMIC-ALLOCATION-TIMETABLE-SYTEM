<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['loggedin_name']) || !isset($_SESSION['loggedin_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_code = $_POST['unit_code'];
    $course_group_codes = $_POST['course_group_code'];
    $day = $_POST['day'];
    $lesson = $_POST['lesson'];
    $room = $_POST['room'];
    $lecturer = $_SESSION['loggedin_name'];
    $staff_no = $_SESSION['loggedin_id'];

    $insert_query = "INSERT INTO make_up (day, lesson, unit_code, room, staff_no, lecturer) VALUES ('$day', '$lesson', '$unit_code', '$room', '$staff_no', '$lecturer')";
    
    if (mysqli_query($conn, $insert_query)) {
        echo '<script>alert("Make-up class booked successfully!"); window.location.href = "lecturerpage.php";</script>';
    } else {
        echo '<script>alert("Failed to book make-up class!"); window.location.href = "makeup.php";</script>';
    }
}
?>
