<?php
include 'connection.php'; // Ensure this file sets up a $conn variable

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_no = $_POST['staff_no'];
    $lecturer_name = $_POST['lecturer_name'];
    $lecturer_email = $_POST['lecturer_email'];
    $unit_ids = $_POST['units'];

    // Start a transaction
    $conn->begin_transaction();

    try {
        // Insert into lecturers table
        $stmt = $conn->prepare("INSERT INTO lecturers (staff_no, lecturer_name, lecturer_email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $staff_no, $lecturer_name, $lecturer_email);
        $stmt->execute();

        // Insert into lecturers_units table (for each selected unit)
        $stmt = $conn->prepare("INSERT INTO lecturer_units (staff_no, unit_id) VALUES (?, ?)");
        foreach ($unit_ids as $unit_id) {
            $stmt->bind_param("si", $staff_no, $unit_id);
            $stmt->execute();
        }

        // Commit the transaction
        $conn->commit();
        echo "<script>alert('Lecturer and units linked successfully!');</script>";
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "<script>alert('Failed to add Lecturer: {$e->getMessage()}');</script>";
    }
}

// Fetch units for the form
$units = $conn->query("SELECT unit_id, unit_name FROM units")->fetch_all(MYSQLI_ASSOC);

// Fetch lecturers and units they teach
$q = $conn->query("
    SELECT l.staff_no, l.lecturer_name, l.lecturer_email, GROUP_CONCAT(u.unit_name SEPARATOR ', ') as taught_units
    FROM lecturers l
    LEFT JOIN lecturer_units lu ON l.staff_no = lu.staff_no
    LEFT JOIN units u ON lu.unit_id = u.unit_id
    GROUP BY l.staff_no
");
$lecturers = $q->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>Manage Lecturers and Units</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!-- Font Awesome CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
    <!-- Custom Style CSS -->
    <link href="assets/css/style.css" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'/>
    <style>
        .scrollable-form {
            max-height: 420px; /* Adjust height as needed */
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ccc;
        }
        .navbar-nav > .dropdown:hover > .dropdown-menu {
            display: block;
            cursor: pointer;
        }
        .dropdown-menu > li > a:hover {
            background-color: #f5f5f5; /* Change the color as needed */
            cursor: pointer;
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
    margin-top: 20px;
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
        margin-bottom: 20px; /* Spacing below the centered text */
    }

    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="navbar navbar-inverse navbar-fixed-top " id="menu">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="navbar-collapse collapse move-me">
            <ul class="nav navbar-nav navbar-left">
                <li><a href="addlecturers.php">ADD LECTURERS</a></li>
                <li><a href="addunits.php">ADD UNITS</a></li>
                <li><a href="rooms.php">ADD ROOMS</a></li>
                <li><a href="addcourse_group.php">ADD COURSE_GROUPS</a></li>
                <li><a href="timeslots.php">TIMESLOTS</a></li>
                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">TIMETABLE
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href=generatetimetable.php>GENERATE TIMETIBALE</a>
                        </li>
                        <li>
                            <a href=viewtimetable.php>VIEW TIMETABLE</a>
                        </li>
                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="index.php">LOGOUT</a></li>
            </ul>
        </div>
    </div>
</div>
<!-- NAVBAR SECTION END -->

<br>

<!-- Add Lecturers Button -->
<div align="center" style="margin-top: 70px">
    <button id="lecturermanual" class="btn btn-success btn-lg">ADD LECTURER</button>
</div>

<!-- Modal for Adding Lecturers -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2 id="popupHead">Add Lecturer</h2>
        </div>
        <div class="modal-body" id="EnterLecturer">
            <div style="display:none" id="addLecturerForm">
                <!-- Form to Add Lecturer -->
                <form action="addlecturers.php" method="POST">
                <div class="scrollable-form">
                    <div class="form-group">
                        <label for="staff_no">Staff Number</label>
                        <input type="text" class="form-control" id="staff_no" name="staff_no"
                               placeholder="Staff Number..." required>
                    </div>
                    <div class="form-group">
                        <label for="lecturer_name">Lecturer Name</label>
                        <input type="text" class="form-control" id="lecturer_name" name="lecturer_name"
                               placeholder="Lecturer's Name..." required>
                    </div>
                    <div class="form-group">
                        <label for="lecturer_email">Lecturer Email</label>
                        <input type="email" class="form-control" id="lecturer_email" name="lecturer_email"
                               placeholder="Lecturer's Email..." required>
                    </div>
                    <div class="form-group">
                        <label for="units">Units</label><br>
                        <!-- PHP Loop to Generate Unit Checkboxes -->
                        <?php foreach ($units as $unit) : ?>
                            <input type="checkbox" name="units[]" value="<?= $unit['unit_id'] ?>">
                            <?= $unit['unit_name'] ?><br>
                        <?php endforeach; ?>
                    </div>
                    <div align="right">
                        <input type="submit" class="btn btn-default" name="ADD" value="ADD">
                    </div>
                </div>
                </form>
            </div>
        </div>
        <div class="modal-footer"></div>
    </div>
</div>

<!-- JavaScript for Modal Behavior -->
<script>
    var modal = document.getElementById('myModal');
    var addlecturerBtn = document.getElementById("lecturermanual");
    var heading = document.getElementById("popupHead");
    var lecturerForm = document.getElementById("addLecturerForm");
    var span = document.getElementsByClassName("close")[0];

    addlecturerBtn.onclick = function () {
        modal.style.display = "block";
        lecturerForm.style.display = "block";
    }

    span.onclick = function () {
        modal.style.display =
        "none";
        lecturerForm.style.display = "none";
    }

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<div align="center">
    <br>
    <style>
    table {
        margin-top: 10px;
        font-family: Arial, sans-serif;
        border-collapse: collapse;
        width: 70%;
    }

    td, th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) td {
        background-color: yellow;
    }

    tr:nth-child(odd) td {
        background-color: white;
    }
</style>

    <table id="lecturerstable">
        <caption><strong>ADDED LECTURERS</strong></caption>
        <tr>
            <th width="100">Staff Number</th>
            <th width="100">Lecturer Name</th>
            <th width="100">Lecturer Email</th>
            <th width="100">Taught Units</th>
            <th width="60">Action</th>
        </tr>
        <?php foreach ($lecturers as $lecturer) : ?>
            <tr>
                <td><?= $lecturer['staff_no'] ?></td>
                <td><?= $lecturer['lecturer_name'] ?></td>
                <td><?= $lecturer['lecturer_email'] ?></td>
                <td><?= $lecturer['taught_units'] ?></td>
                <td><button>Delete</button></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <br>
    <br>
    <br>
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
</body>
</html>
