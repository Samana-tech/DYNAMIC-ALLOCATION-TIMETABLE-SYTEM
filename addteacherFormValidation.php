<?php
include 'connection.php'; // Ensure this file sets up a $conn variable

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $staff_no = $_POST['staff_no'];
    $lecturer_name = $_POST['lecturer_name'];
    $lecturer_email = $_POST['lecturer_email'];
    $unit_ids = isset($_POST['units']) ? $_POST['units'] : [];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into lecturers table
        $stmt = $conn->prepare("INSERT INTO lecturers (staff_no, lecturer_name, lecturer_email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $staff_no, $lecturer_name, $lecturer_email);
        $stmt->execute();

        // Insert into lecturer_units table (for each selected unit)
        $stmt = $conn->prepare("INSERT INTO lecturer_units (staff_no, unit_id) VALUES (?, ?)");
        foreach ($unit_ids as $unit_id) {
            $stmt->bind_param("si", $staff_no, $unit_id);
            $stmt->execute();
        }

        // Commit the transaction
        $conn->commit();
        echo "<script>alert('Lecturer and units linked successfully!');</script>";
        header("Location: addlecturers.php"); // Redirect after successful insertion
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "<script>alert('Failed to add Lecturer: {$e->getMessage()}');</script>";
        // Optionally handle error display or redirection
    }
} else {
    // Redirect if accessed directly without POST data
    header("Location: addlecturers.php");
    exit();
}
?>
