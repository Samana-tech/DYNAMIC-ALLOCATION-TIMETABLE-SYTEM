<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_code = $_POST['unit_code'];
    $course_group_codes = explode(',', $_POST['course_group_codes']);
    $day = $_POST['day'];
    $lesson = $_POST['lesson'];
    $room = $_POST['room'];
    $lecturer = $_SESSION['loggedin_name'];
    $staff_no = $_SESSION['loggedin_id'];

    // Check for collisions for the lecturer
    $collision_query_lecturer = "
        SELECT * FROM (
            SELECT day, lesson, staff_no FROM timetable WHERE day='$day' AND lesson='$lesson' AND staff_no='$staff_no'
            UNION
            SELECT day, lesson, staff_no FROM make_up WHERE day='$day' AND lesson='$lesson' AND staff_no='$staff_no'
        ) AS combined_results
    ";

    $collision_result_lecturer = mysqli_query($conn, $collision_query_lecturer);

    if (mysqli_num_rows($collision_result_lecturer) > 0) {
        echo '<script>alert("You already have a class scheduled at this time. Please choose a different time or day."); window.location.href = "makeup.php";</script>';
        exit();
    }

    // Check for collisions for each course group
    $course_group_collision = false;

    foreach ($course_group_codes as $course_group_code) {
        $collision_query_group = "
            SELECT * FROM (
                SELECT day, lesson, course_group_code FROM timetable WHERE day='$day' AND lesson='$lesson' AND course_group_code='$course_group_code'
                UNION
                SELECT day, lesson, course_group_code FROM make_up WHERE day='$day' AND lesson='$lesson' AND course_group_code='$course_group_code'
            ) AS combined_results
        ";

        $collision_result_group = mysqli_query($conn, $collision_query_group);

        if (mysqli_num_rows($collision_result_group) > 0) {
            $course_group_collision = true;
            break;
        }
    }

    if ($course_group_collision) {
        echo '<script>alert("One or more of the selected course groups already have a class scheduled at this time. Please choose a different time or day."); window.location.href = "makeup.php";</script>';
        exit();
    }

    // No collisions, proceed to insert the make-up class
    $insert_query = "INSERT INTO make_up (day, lesson, unit_code, room, staff_no, lecturer, course_group_code) VALUES ('$day', '$lesson', '$unit_code', '$room', '$staff_no', '$lecturer', '$course_group_code')";
    
    if (mysqli_query($conn, $insert_query)) {
        echo '<script>alert("Make-up class booked successfully!"); window.location.href = "lecturerpage.php";</script>';
    } else {
        echo '<script>alert("Failed to book make-up class!"); window.location.href = "makeup.php";</script>';
    }
} else {
    header("Location: makeup.php");
    exit();
}
?>

