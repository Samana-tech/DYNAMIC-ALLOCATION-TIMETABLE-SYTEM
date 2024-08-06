<?php
// Include database connection file
include 'connection.php';

// Check if the form data has been submitted
if (isset($_POST['room_name']) && isset($_POST['capacity'])) {
    // Sanitize inputs to prevent SQL injection
    $room_name = mysqli_real_escape_string($conn, $_POST['room_name']);
    $capacity = (int)$_POST['capacity']; // Convert capacity to integer

    // Validate inputs (You can add more validation as per your requirements)
    if (empty($room_name) || $capacity <= 0) {
        $message = "Room name and capacity are required.";
        echo "<script type='text/javascript'>alert('$message');</script>";
        die(); // Stop further execution
    }

    // Prepare and execute query to insert data into 'rooms' table
    $insertQuery = "INSERT INTO rooms (room_name, capacity) VALUES ('$room_name', $capacity)";
    $result = mysqli_query($conn, $insertQuery);

    if ($result) {
        $message = "Room added successfully.";
        echo "<script type='text/javascript'>alert('$message');</script>";
        header("Location: rooms.php"); // Redirect back to rooms.php after successful insertion
    } else {
        $message = "Failed to add room. Please try again.";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // Optionally handle the error scenario
    }
} else {
    // Redirect or show an error message if accessed directly without form submission
    $message = "Access denied.";
    echo "<script type='text/javascript'>alert('$message');</script>";
    // Optionally redirect user to an appropriate page
}
?>
