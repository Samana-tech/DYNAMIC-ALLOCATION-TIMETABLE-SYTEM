<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit_name = $_POST['unit_name'];
    $unit_code = $_POST['unit_code'];
    $description = $_POST['description'];

    // Insert into units table
    $stmt = $conn->prepare("INSERT INTO units (unit_name, unit_code, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $unit_name, $unit_code, $description);
    if ($stmt->execute()) {
        echo "<script>alert('Unit added successfully!');</script>";
    } else {
        echo "<script>alert('Failed to add unit.');</script>";
    }
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <meta name="description" content=""/>
    <meta name="author" content=""/>
    <title>Dynamic Allocation TimeTable System</title>
    <!-- BOOTSTRAP CORE STYLE CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet"/>
    <!-- FONT AWESOME CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
    <!-- FLEXSLIDER CSS -->
    <link href="assets/css/flexslider.css" rel="stylesheet"/>
    <!-- CUSTOM STYLE CSS -->
    <link href="assets/css/style.css" rel="stylesheet"/>
    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'/>
    <style>
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
<!--NAVBAR SECTION END-->
<br>

<div align="center"
     style="margin-top:10%">
    <button
        id="unitmanual" class="btn btn-success btn-lg">ADD UNIT
    </button>
</div>

<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h2 id="popupHead">Add Unit</h2>
        </div>
        <div class="modal-body" id="EnterUnit">
            <!-- Unit Form -->
            <div style="display:none" id="addUnitForm">
                <form action="addunits.php" method="POST">
                    <div class="form-group">
                        <label for="unit_name">Unit Name</label>
                        <input type="text" class="form-control" id="unit_name" name="unit_name"
                               placeholder="Unit Name..." required>
                    </div>
                    <div class="form-group">
                        <label for="unit_code">Unit Code</label>
                        <input type="text" class="form-control" id="unit_code" name="unit_code"
                               placeholder="Unit Code..." required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="Description..."></textarea>
                    </div>
                    <div align="right" class="form-group">
                        <input type="submit" class="btn btn-default" name="ADD" value="ADD">
                    </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById('myModal');

    // Get the button that opens the modal
    var addunitBtn = document.getElementById("unitmanual");
    var heading = document.getElementById("popupHead");
    var unitForm = document.getElementById("addUnitForm");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal
    addunitBtn.onclick = function () {
        modal.style.display = "block";
        unitForm.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function () {
        modal.style.display = "none";
        unitForm.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<!-- Add units Table -->
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

<table id="unitstable">
    <caption><strong>ADDED UNITS</strong></caption>
    <tr>
        <th width="100">Unit Name</th>
        <th width="100">Unit Code</th>
        <th width="100">Description</th>
        <th width="60">Action</th>
    </tr>
    <?php
    $q = $conn->query("
        SELECT unit_name, unit_code, description
        FROM units
    ");

    while ($row = $q->fetch_assoc()) {
        echo "<tr>
                <td>{$row['unit_name']}</td>
                <td>{$row['unit_code']}</td>
                <td>{$row['description']}</td>
                <td><button>Delete</button></td>
              </tr>\n";
    }
    ?>
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
