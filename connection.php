<?php
$conn = mysqli_connect("localhost", "root", "", "timetabledb");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>