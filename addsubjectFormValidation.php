<?php
include 'connection.php';

// Check if form fields are set
if (isset($_POST['unit_name']) && isset($_POST['unit_code']) && isset($_POST['description'])) {
    $unit_name = $_POST['unit_name'];
    $unit_code = $_POST['unit_code'];
    $description = $_POST['description'];

    // Insert into units table
    $stmt = $conn->prepare("INSERT INTO units (unit_name, unit_code, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $unit_name, $unit_code, $description);

    if ($stmt->execute()) {
        $message = "Unit added.";
        echo "<script type='text/javascript'>alert('$message');</script>";
        header("Location: addunits.php");
        exit();
    } else {
        $message = "Failed to add unit. Please try again.";
        echo "<script type='text/javascript'>alert('$message');</script>";
        // Optionally handle error display or redirection
    }
} else {
    $message = "Incomplete form submission. Please fill in all required fields.";
    echo "<script type='text/javascript'>alert('$message');</script>";
    // Optionally handle error display or redirection
}
?>
