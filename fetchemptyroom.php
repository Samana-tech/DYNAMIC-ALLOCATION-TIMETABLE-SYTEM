<?php
session_start();
include 'connection.php';

if (!isset($_GET['unit_code']) || !isset($_GET['course_group_code']) || !isset($_GET['day']) || !isset($_GET['lesson'])) {
    header("Location: changelesson.php");
    exit();
}

$unit_code = $_GET['unit_code'];
$course_group_code = $_GET['course_group_code'];
$day = $_GET['day'];
$lesson = $_GET['lesson'];

list($start_time, $end_time) = explode(' - ', $lesson);

$occupied_rooms = [];
$query = "SELECT room FROM timetable WHERE day='$day' AND lesson='$lesson'";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $occupied_rooms[] = $row['room'];
}

$empty_rooms_query = "SELECT room_name FROM rooms WHERE room_name NOT IN ('" . implode("','", $occupied_rooms) . "')";
$empty_rooms_result = mysqli_query($conn, $empty_rooms_query);
?>

<!DOCTYPE html>
<html>
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
        .scrollable-form {
            max-height: 500px; /* Adjust height as needed */
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ccc;
        }

        .navbar-nav > .dropdown:hover > .dropdown-menu {
            display: block;
        }
        .dropdown-menu > li > a:hover {
            background-color: #f5f5f5; /* Change the color as needed */
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
    .custom-caption {
        /* background-color: #ffffff; White background */
        color: #ffffff; /* Text color */
        font-weight: bold; /* Bold text */
        padding: 10px; /* Padding around the caption */
        text-align: center; /* Center-align text */
    }

    </style>
</head>
<body>
    <!-- Navbar -->
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
                <li><a href="lecturerpage.php">Hello <?php echo $_SESSION['loggedin_name']; ?></a></li>
                <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">TIMETABLE
                        <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href=changelesson.php>CHANGE LESSON</a>
                        </li>
                        <li>
                            <a href=makeup.php>BOOK MAKE_UP LESSON</a>
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
    <!-- End Navbar -->

    <div class="container" style="margin-top: 70px;">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <h2 class="custom-caption">Available Rooms for <?php echo $lesson; ?> on <?php echo $day; ?></h2>
                <form method="POST" action="changelesson_process.php" class="scrollable-form">
                    <input type="hidden" name="unit_code" value="<?php echo $unit_code; ?>">
                    <input type="hidden" name="course_group_code" value="<?php echo $course_group_code; ?>">
                    <input type="hidden" name="day" value="<?php echo $day; ?>">
                    <input type="hidden" name="lesson" value="<?php echo $lesson; ?>">
                    
                    <div class="form-group">
                        <label for="room" class="custom-caption">Select Room:</label>
                        <select name="room" class="form-control" required>
                            <?php
                            while ($row = mysqli_fetch_assoc($empty_rooms_result)) {
                                echo "<option value='{$row['room_name']}'>{$row['room_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Change Schedule</button>
                </form>
            </div>
        </div>
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
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br>
    <br><br>
    
    
</body>
</html>
