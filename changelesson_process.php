<?php
session_start();
include 'connection.php';

if (!isset($_SESSION['loggedin_name']) || !isset($_SESSION['loggedin_id'])) {
    header("Location: login.php");
    exit();
}

$staff_no = $_SESSION['loggedin_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_code = $_POST['unit_code'];
    $course_group_code = $_POST['course_group_code'];
    $day = $_POST['day'];
    $lesson = $_POST['lesson'];
    $room = $_POST['room'];

    $update_query = "UPDATE timetable 
                     SET day='$day', lesson='$lesson', room='$room' 
                     WHERE unit='$unit_code' AND staff_no='$staff_no'";

    if (mysqli_query($conn, $update_query)) {
        echo '<script>
                alert("Schedule updated successfully!");
                window.location.href = "lecturerpage.php";
              </script>';
    } else {
        echo '<script>
                alert("Failed to update schedule. Please try again.");
                window.location.href = "changelesson.php";
              </script>';
    }
}
?>
