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

// Clear existing timetable data
$conn->query("DELETE FROM timetable");

// Fetch data from the database
$rooms = fetchData($conn, "rooms");
$course_groups = fetchData($conn, "course_groups");
$lecturers = fetchData($conn, "lecturers");
$timeslots = fetchData($conn, "timeslots");
$units = fetchData($conn, "units");
$lecturer_units = fetchData($conn, "lecturer_units");
$course_groups_units = fetchData($conn, "course_group_units");

// Generate timetable
$timetable = generateTimetable($rooms, $course_groups, $lecturers, $timeslots, $units, $lecturer_units, $course_groups_units);

if ($timetable !== false) {
    // Insert the generated timetable into the database
    insertTimetableToDatabase($conn, $timetable);

    // Store timetable in session for display
    $_SESSION['timetable'] = $timetable;

    // Close the database connection
    $conn->close();

    // Redirect to viewtimetable.php
    echo '<script>
            alert("Timetable generated successfully!");
            window.location.href = "viewtimetable.php";
        </script>';
    exit(); // Ensure no further code is executed after redirection
} else {
    echo '<script>alert("Failed to generate timetable!");</script>';
}

// Close the database connection
$conn->close();

// Function to fetch data from the database
function fetchData($conn, $table) {
    $query = "SELECT * FROM $table";
    $result = mysqli_query($conn, $query);

    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }

    return $data;
}

// Function to generate timetable
function generateTimetable($rooms, $course_groups, $lecturers, $timeslots, $units, $lecturer_units, $course_groups_units) {
    $timetable = [];
    $scheduledUnits = [];

    foreach ($units as $unit) {
        foreach ($course_groups_units as $cgu) {
            if ($cgu['unit_id'] == $unit['unit_id']) {
                $group_id = $cgu['course_group_id'];
                foreach ($lecturer_units as $lu) {
                    if ($lu['unit_id'] == $unit['unit_id']) {
                        $lecturer_id = $lu['staff_no'];
                        $scheduled = false;
                        foreach ($timeslots as $timeslot) {
                            if (!$scheduled) {
                                foreach ($rooms as $room) {
                                    if (!$scheduled) {
                                        if (!hasConflict($timetable, $timeslot, $room, $lecturer_id, $group_id)) {
                                            // Add to timetable
                                            $timetable[] = [
                                                'day' => $timeslot['day'],
                                                'lesson' => $timeslot['start_time'] . ' - ' . $timeslot['end_time'],
                                                'unit' => $unit['unit_code'],
                                                'room' => $room['room_name'],
                                                'staff_no' => $lecturer_id,
                                                'lecturer' => $lecturers[array_search($lecturer_id, array_column($lecturers, 'staff_no'))]['lecturer_name'],
                                                'course_group' => $group_id,
                                                'course' => $course_groups[array_search($group_id, array_column($course_groups, 'course_group_id'))]['course_group_code']
                                            ];
                                            $scheduledUnits[] = $unit['unit_id'];
                                            $scheduled = true;
                                            break; // Break out of room loop
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return $timetable;
}

// Function to check for conflicts
function hasConflict($timetable, $timeslot, $room, $lecturer_id, $group_id) {
    foreach ($timetable as $entry) {
        if (
            $entry['day'] == $timeslot['day'] &&
            $entry['lesson'] == $timeslot['start_time'] . ' - ' . $timeslot['end_time'] &&
            ($entry['room'] == $room['room_name'] || $entry['staff_no'] == $lecturer_id || $entry['course_group'] == $group_id)
        ) {
            return true; // Conflict found
        }
    }

    return false; // No conflict
}

// Function to insert timetable into the database
function insertTimetableToDatabase($conn, $timetable) {
    $sno = 1;
    foreach ($timetable as $entry) {
        $sql = "INSERT INTO timetable (SNO, DAY, LESSON, unit, room, staff_no, lecturer, course_group, course)
                VALUES ('$sno', '{$entry['day']}', '{$entry['lesson']}', '{$entry['unit']}', '{$entry['room']}', '{$entry['staff_no']}', '{$entry['lecturer']}', '{$entry['course_group']}', '{$entry['course']}')";
        mysqli_query($conn, $sql);
        $sno++;
    }
}
?>
